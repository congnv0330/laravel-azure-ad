<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Response format json data
     */
    public function response(
        mixed $data,
        int $status = Response::HTTP_OK,
    ): JsonResponse {
        return response()->json(
            data : ['data' => $data],
            status: $status
        );
    }

    /**
     * Response format json created data
     */
    public function responseCreated(Model $model): JsonResponse
    {
        return $this->response(
            ['id' => $model->{$model->getKeyName()}],
            Response::HTTP_CREATED,
        );
    }

    /**
     * Response no content
     */
    public function responseNoContent(): Response
    {
        return response()->noContent();
    }
}
