<?php

namespace App\Services\Requests\Create;

use App\Enums\Requests\Status;
use App\Exceptions\GeneralDatabaseException;
use App\Exceptions\RabbitMQException;
use App\Models\RequestHistory;
use App\Services\Requests\Create\Pipes\AddToCaptionGenerationQueue;
use App\Services\Requests\Create\Pipes\InsertModel;
use App\Services\Requests\Create\Pipes\S3Upload;
use Exception;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Throwable;

class Service
{
    private array $pipes = [
        // here comes the steps of creating a request model:
        InsertModel::class,
        S3Upload::class,
        AddToCaptionGenerationQueue::class
    ];

    /**
     * @param RequestHistory $request
     * @return int
     * @throws RabbitMQException
     * @throws GeneralDatabaseException
     * @throws Exception
     */
    public function create(RequestHistory $request): int
    {
        try {
            DB::beginTransaction();

            app(Pipeline::class)
                ->send($request)
                ->through($this->pipes)
                ->thenReturn();

            if ($request['id']) {
                DB::commit();
                return $request['id'];
            }

            DB::rollBack();
            return -1;
        } catch (GeneralDatabaseException $exception) {
            DB::rollBack();
            throw $exception;
        } catch (RabbitMQException $exception) {
            $request->update(['status' => Status::FAILURE]);
            throw $exception;
        } catch (Throwable) {
            $request->update(['status' => Status::FAILURE]);
            throw new Exception('Server Error!');
        }
    }
}
