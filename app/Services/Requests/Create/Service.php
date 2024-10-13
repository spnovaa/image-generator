<?php

namespace App\Services\Requests\Create;

use App\Exceptions\GeneralDatabaseException;
use App\Exceptions\RabbitMQException;
use App\Models\RequestHistory;
use App\Services\Requests\Create\Pipes\AddToCaptionGenerationQueue;
use App\Services\Requests\Create\Pipes\InsertModel;
use App\Services\Requests\Create\Pipes\S3Upload;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;

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
     */
    public function create(RequestHistory $request): int
    {
        try {
            DB::beginTransaction();

            app(Pipeline::class)
                ->send($request)
                ->through($this->pipes)
                ->thenReturn();

            if ($request['R_Id']) {
                DB::commit();
                return $request['R_Id'];
            }

            DB::rollBack();
            return -1;
        } catch (RabbitMQException|GeneralDatabaseException $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
