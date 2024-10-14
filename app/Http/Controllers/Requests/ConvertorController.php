<?php

namespace App\Http\Controllers\Requests;

use App\Enums\Requests\Status;
use App\Exceptions\GeneralDatabaseException;
use App\Exceptions\RabbitMQException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Requests\RequestHistoryResource;
use App\Models\RequestHistory;
use App\Services\Requests\Service;
use Illuminate\Http\Request;
use Throwable;

class ConvertorController extends Controller
{
    public function __construct(
        private Service $service
    )
    {
    }

    public function store(Request $request)
    {
        try {
            $id = $this->service->create($this->requestToModel($request));
            return response()->json([
                'status' => 'success',
                'id' => $id
            ]);
        } catch (GeneralDatabaseException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error!'
            ], 530);
        } catch (RabbitMQException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message Broker Error!'
            ], 502);
        } catch (Throwable $throwable) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server Error!'
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $model = $this->service->show($id);
            return response()->json(new RequestHistoryResource($model));
        } catch (GeneralDatabaseException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error!'
            ], 530);
        } catch (RabbitMQException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message Broker Error!'
            ], 502);
        } catch (Throwable $throwable) {
            dd($throwable);
            return response()->json([
                'status' => 'error',
                'message' => 'Server Error!'
            ], 500);
        }
    }

    public function index()
    {
        try {
            return response()->json(
                RequestHistoryResource::collection($this->service->index())
            );
        } catch (Throwable) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server Error!'
            ], 500);
        }
    }

    private function requestToModel(Request $request)
    {
        $model = new RequestHistory([
            'R_Email' => $request->email,
            'R_Status' => Status::PENDING
        ]);
        $model['img'] = $request->img;

        return $model;
    }
}
