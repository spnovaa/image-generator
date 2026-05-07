# 🎨 Image Generator

A Laravel 11 service that turns user-uploaded images into AI-generated
re-imaginings, e-mailing the result when ready.

```
upload image  ──►  caption it (AI)  ──►  generate new image (AI)  ──►  notify user
```

The codebase is organised around **Hexagonal Architecture** principles —
domain code depends only on narrow port interfaces, with infrastructure
(HuggingFace, S3, Eloquent, RabbitMQ) confined to swappable adapters.

---

## High-Level Flow

```
┌──────────────┐  POST /converts   ┌──────────────────┐
│  Client      │ ────────────────► │  ConvertorCtrl   │
└──────────────┘                   └────────┬─────────┘
                                            │ StoreConvertRequest → DTO
                                            ▼
                                   ┌──────────────────┐
                                   │  Create Pipeline │  (Laravel Pipeline)
                                   │  ─────────────── │
                                   │  PersistRecord   │
                                   │  UploadOriginal  │── ObjectStorage port
                                   │  DispatchJob     │── RabbitMQ
                                   └────────┬─────────┘
                                            │
                                            ▼
┌────────────────────────┐  worker  ┌──────────────────┐
│ GenerateImageCaption   │ ───────► │ Captioning Pipe  │
│ (queued job)           │          │  Download img    │── ObjectStorage
└────────────────────────┘          │  Generate cap.   │── ImageCaptioner
                                    │  Persist record  │── Repository
                                    └────────┬─────────┘
                                             │ status=READY
                                             ▼
┌────────────────────────┐  cron    ┌──────────────────┐
│ app:inquire            │ ───────► │ ImageGen Pipe    │
│ (every minute)         │          │  Generate img    │── ImageGenerator
└────────────────────────┘          │  Upload img      │── ObjectStorage
                                    │  Persist record  │── Repository
                                    │  Announce event  │── ImageGenerated 📣
                                    └────────┬─────────┘
                                             │
                                             ▼
                                    ┌──────────────────┐
                                    │ SendNotification │
                                    │ (queued listener)│── Mailer
                                    └──────────────────┘
```

---

## Design Patterns Applied

| Pattern                       | Where                                   | Why                                                                                       |
| ----------------------------- | --------------------------------------- | ----------------------------------------------------------------------------------------- |
| **Hexagonal / Ports & Adapters** | `app/Contracts` ↔ `app/Adapters`     | Domain depends on narrow ports; swap HuggingFace, S3, etc. without touching domain code.   |
| **Adapter**                   | `HuggingFaceImageCaptioner`, `HuggingFaceImageGenerator`, `S3ObjectStorage` | Translate vendor SDKs to our domain ports.                                  |
| **Repository**                | `RequestHistoryRepository` + `EloquentRequestHistoryRepository` | Hides Eloquent behind an interface; tests use an in-memory fake.            |
| **Pipeline (Chain of Responsibility)** | `app/Services/Requests/*/Service.php` + `*/Pipes/*.php` | Each step does one thing; reorder/replace without rewriting.                |
| **DTO**                       | `CreateRequestData`, `PipelinePayload` | Typed data carriers replacing in-place mutation of an Eloquent model.                       |
| **Domain Event / Observer**   | `ImageGenerated` + `SendGeneratedImageNotification` | Decouples side-effects (e-mail, webhooks) from the generation pipeline.                    |
| **Form Request**              | `StoreConvertRequest`                  | Centralises validation + maps to DTO; controller stays free of mapping logic.               |
| **Service Provider (DI)**     | `DomainServiceProvider`                | Single place where ports bind to adapters and infrastructure clients are constructed.        |
| **Native Enum (Value)**       | `RequestStatus`, `HuggingFaceEndpoint` | Type-safe replacements for the previous `abstract class` constant bags.                      |

---

## Project Structure

```
app/
├── Adapters/                     # Concrete adapters (infrastructure)
│   ├── HuggingFace/
│   │   ├── HuggingFaceImageCaptioner.php
│   │   └── HuggingFaceImageGenerator.php
│   └── Storage/
│       └── S3ObjectStorage.php
├── Console/Commands/Inquire.php  # Cron: poll READY records
├── Contracts/                    # Domain ports (interfaces)
│   ├── ImageCaptioner.php
│   ├── ImageGenerator.php
│   ├── ObjectStorage.php
│   └── RequestHistoryRepository.php
├── Data/                         # Immutable / typed data carriers
│   ├── CreateRequestData.php
│   └── PipelinePayload.php
├── Enums/                        # Native PHP 8.1 enums
│   ├── HuggingFaceEndpoint.php
│   └── RequestStatus.php
├── Events/ImageGenerated.php
├── Exceptions/                   # Domain exceptions
├── Http/
│   ├── Controllers/Requests/ConvertorController.php
│   ├── Requests/StoreConvertRequest.php
│   └── Resources/Requests/RequestHistoryResource.php
├── Jobs/GenerateImageCaption.php
├── Listeners/SendGeneratedImageNotification.php
├── Mail/ImageGeneratorMail.php
├── Models/RequestHistory.php
├── Providers/
│   ├── AppServiceProvider.php
│   ├── DomainServiceProvider.php # Ports → adapters
│   └── EventServiceProvider.php
├── Repositories/EloquentRequestHistoryRepository.php
└── Services/Requests/
    ├── Service.php               # Application-service facade
    ├── Create/Service.php        + Pipes/{PersistRecord,UploadOriginalImage,DispatchCaptioningJob}
    ├── Captioning/Service.php    + Pipes/{DownloadOriginalImage,GenerateCaption,PersistCaption}
    └── ImageGenerating/Service.php + Pipes/{GenerateImage,UploadGeneratedImage,PersistGeneratedRecord,AnnounceImageGenerated}
```

---

## API

### `POST /converts` — Submit a new conversion request

Multipart form fields:

| Field   | Required | Description                                            |
| ------- | -------- | ------------------------------------------------------ |
| `email` | yes      | E-mail to notify when the result is ready.             |
| `img`   | yes      | Source image (`jpeg`, `png`, `webp`; max 4 MB by default). |

Response `201 Created`:

```json
{ "status": "success", "id": 42 }
```

### `GET /converts/{id}` — Inspect a request

```json
{
  "id": 42,
  "email": "user@example.com",
  "status": "done",
  "caption": "a cat on a sofa",
  "url": "https://…/42.jpg"
}
```

### `GET /converts?per_page=20` — List requests (paginated)

---

## Configuration

All configuration is centralised under `config/`:

| File                          | Concerns                                                      |
| ----------------------------- | ------------------------------------------------------------- |
| `config/huggingface.php`      | HuggingFace base URL, token, timeout, TLS verification.       |
| `config/image_generator.php`  | Bucket names, queue names, mail-sender, validation limits.    |
| `config/filesystems.php` (s3) | S3 endpoint, region, credentials, scheme, path-style flag.    |

Domain code never calls `env()` directly — only `config()`.

---

## Testing

```bash
composer install
php artisan test
```

Two unit tests demonstrate the value of the port-adapter design:

- **`CaptioningServiceTest`** — exercises the captioning pipeline end-to-end with **zero infrastructure**, swapping in `FakeImageCaptioner`, `FakeObjectStorage`, and `InMemoryRequestHistoryRepository` through the container.
- **`ImageGeneratingServiceTest`** — same idea for image generation, additionally asserting that the `ImageGenerated` domain event is dispatched.

`tests/Fakes/` contains the in-memory test doubles.

---

## Local Development

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan queue:work --queue=GenerateImageCaption   # captioning worker
php artisan schedule:work                              # runs the inquire cron
php artisan serve
```

---

## License

MIT.
