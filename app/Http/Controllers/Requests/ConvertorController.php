<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConvertRequest;
use App\Http\Resources\Requests\RequestHistoryResource;
use App\Services\Requests\Service as RequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpStatus;

/**
 * HTTP entry point for the conversion-request resource.
 *
 * Validation is delegated to {@see StoreConvertRequest}, error mapping
 * to the global exception handler (see {@see \bootstrap\app.php}), and
 * persistence + side-effects to {@see RequestService}.
 */
final class ConvertorController extends Controller
{
    public function __construct(
        private readonly RequestService $service,
    ) {}

    public function store(StoreConvertRequest $request): JsonResponse
    {
        $history = $this->service->create($request->toData());

        return response()->json(
            ['status' => 'success', 'id' => $history->id],
            HttpStatus::HTTP_CREATED,
        );
    }

    public function show(int $id): JsonResponse
    {
        $history = $this->service->show($id);

        if ($history === null) {
            return response()->json(
                ['status' => 'error', 'message' => 'Not found.'],
                HttpStatus::HTTP_NOT_FOUND,
            );
        }

        return response()->json(new RequestHistoryResource($history));
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);

        return response()->json(
            RequestHistoryResource::collection(
                $this->service->paginate($perPage),
            ),
        );
    }
}
