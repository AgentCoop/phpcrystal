<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Http\Controllers\AbstractController;

abstract class AbstractApiController extends AbstractController
{
    const RESPONSE_STATUS_SUCCESS = 'success';
    const RESPONSE_STATUS_FAILED = 'failed';

    /**
     * @return void
    */
    protected function checkIfAdmin()
    {
        if (Auth::check() && (Auth::user()->isAdmin())) {
            return;
        }

        throw new \RuntimeException('Not enough permissions');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function failed($customCode = -1, $error, $customData = null)
    {
        $response['code'] = $customCode;
        $response['error'] = $error;

        if ( ! empty($customData)) {
            $response['data'] = $customData;
        }

        // 409 Conflict. Indicates that the request could not be processed because of conflict
        // in the request, such as an edit conflict between multiple simultaneous updates.
        return response()
            ->json($response)
            ->setStatusCode(409);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = [])
    {
        $response = ['status' => self::RESPONSE_STATUS_SUCCESS];

        if ( ! empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function forbidden()
    {
        return response()->json(null, 403);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function serverError($errMessage)
    {
        return response()->json(['error' => $errMessage], 500);
    }

    /**
     * The request requires user authentication
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthorized()
    {
        return response()->json(null, 401);
    }
}
