<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Payout API Documentation",
 *     description="API documentation for MFS and Bank Payout System",
 *     @OA\Contact(
 *         email="support@faastpay.xyz"
 *     )
 * )
 *
 * @OA\Server(
 *     url="https://payoutsystem.faastpay.xyz"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * /**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints for login and token"
 * )
 *
 * @OA\Tag(
 *     name="MFS Payouts",
 *     description="Endpoints for MFS payout operations"
 * )
 *
 * @OA\Tag(
 *     name="Bank Payouts",
 *     description="Endpoints for Bank payout operations"
 * )
 *
 */



class SwaggerController extends Controller
{
    // Empty on purpose
}
