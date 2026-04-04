<?php

namespace App\Http\Controllers;


/**
 * @OA\Info(
 *     title="Elite Guard API",
 *     version="1.0.0",
 *     description="API documentation for the Elite Guard application."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}
