<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponser
{
    /**
     * Handle a Success Response
     *
     * @param  mixed $data
     * @param  mixed $message
     * @param  mixed $code
     * @return void
     */
    protected function successResponse(
        $data,
        $message = null,
        $code = Response::HTTP_OK
    ) {
        return response([
            'status' => 'Success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Handle an Error Response
     *
     * @param  mixed $message
     * @param  mixed $date
     * @param  mixed $code
     * @return void
     */
    protected function errorResponse(
        $message = null,
        $data = null,
        $code = null
    ) {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Handle a Wrong Data Response
     *
     * @param  mixed $message
     * @return void
     */
    protected function wrongDataResponse($message = null)
    {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
            'data' => null,
        ], 422);
    }

    /**
     * Handle a Not Found Response
     *
     * @param  mixed $message
     * @param  mixed $code
     * @return void
     */
    protected function notFoundResponse(
        $message = null,
        $code = Response::HTTP_NOT_FOUND
    ) {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
            'data' => null,
        ], $code);
    }

    /**
     * Handle a Too Many Requests Response
     *
     * @param  mixed $message
     * @param  mixed $code
     * @return void
     */
    protected function tooManyRequests(
        $message = null,
        $code = Response::HTTP_TOO_MANY_REQUESTS
    ) {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
            'data' => null,
        ], $code);
    }

    /**
     * Handle a Failure Response
     *
     * @param mixed $message
     * @param mixed $status
     * @return void
     */
    protected function failure(
        $message,
        $status = Response::HTTP_INTERNAL_SERVER_ERROR
    ) {
        return response()->json([
            'success' => false,
            'message' => $message,
            'current_timestamp' => date("Y-m-d H:i:s"),
        ], $status);
    }

    /**
     * Handle an Unauthorized Error Response
     *
     * @param  mixed $message
     * @param  mixed $date
     * @param  mixed $code
     * @return void
     */
    protected function unauthorized(
        $message = null,
        $code = Response::HTTP_UNAUTHORIZED
    ) {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
        ], $code);
    }
}
