<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * Возврат ошибок
     *
     * @param \Exception $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getErrorResponse(\Exception $exception)
    {
        if ($exception instanceof ModelException){
            return response()->json([
                'errors' => $exception->getErrors(),
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Во время выполнения операции возникла ошибка',
            'message_for_developer' => '',
        ], $exception->getCode());
    }
}
