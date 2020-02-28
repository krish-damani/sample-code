<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

/**
  * @OA\Info(
  *    description="Authentication apis",
  *    version="1.0.0",
  *    title="Flix_Connect API",
  * )
 */

/**
    @OA\SecurityScheme(
        securityScheme="bearerAuth",
        type="http",
        scheme="bearer",
        bearerFormat="JWT"
    ),
 */
class AuthController extends ApiBaseController
{
    /** @var AuthService $authService */
    private $authService;

    /**
     * AuthController constructor.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
        @OA\Post(
            path="/api/login",
            tags={"Login"},
            summary="Login",
            operationId="login",

            @OA\Parameter(
                name="username",
                in="query",
                required=true,
                @OA\Schema(
                    type="string"
                )
            ),
            @OA\Parameter(
                name="password",
                in="query",
                required=true,
                @OA\Schema(
                    type="string"
                )
            ),
            @OA\Parameter(
                name="device_id",
                in="query",
                required=true,
                @OA\Schema(
                    type="string"
                )
            ),
            @OA\Parameter(
                name="device_type",
                in="query",
                required=true,
                @OA\Schema(
                    type="string"
                )
            ),
            @OA\Parameter(
                name="push_token",
                in="query",
                required=true,
                @OA\Schema(
                    type="string"
                )
            ),
            @OA\Parameter(
                name="client-id",
                in="header",
                required=true,
                @OA\Schema(
                    type="string"
                )
            ),
            @OA\Parameter(
                name="client-secret",
                in="header",
                required=true,
                @OA\Schema(
                    type="string"
                )
            ),
            @OA\Parameter(
                name="Content-Type",
                in="header",
                required=true,
                @OA\Schema(
                    type="string"
                )
            ),
            @OA\Response(
                  response=200,
                  description="Success",
                  @OA\MediaType(
                      mediaType="application/json",
                  )
            ),
            @OA\Response(
                response=401,
                description="Unauthorized"
            ),
            @OA\Response(
                response=400,
                description="Invalid request"
            ),
            @OA\Response(
                response=404,
                description="not found"
            ),
      )
     */
    /**
     * Authenticate user.
     *
     * @param  LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->authenticateUser($request);

        return $result ?
            $this->sendResponse($result, Response::HTTP_OK) :
            $this->sendResponse([], Response::HTTP_UNAUTHORIZED, __('exceptions.unauthorized'));
    }

    /**
    @OA\Post(
        path="/api/logout",
        tags={"Logout"},
        summary="Logout",
        operationId="logout",

        @OA\Parameter(
            name="device_id",
            in="query",
            required=true,
            @OA\Schema(
                type="string"
            )
        ),
        @OA\Parameter(
            name="device_type",
            in="query",
            required=true,
            description="should be 0/1",
            @OA\Schema(
            type="string"
            )
        ),
        @OA\Parameter(
            name="Content-Type",
            in="header",
            required=true,
            @OA\Schema(
            type="string"
            )
        ),
        @OA\Parameter(
            name="Accept",
            in="header",
            required=true,
            description="its value will be -> application/json",
            @OA\Schema(
                type="string"
            )
        ),
        @OA\Parameter(
            name="Authorization",
            in="header",
            description="Bearer authorization_token received from login api.",
            required=true,
            @OA\Schema(
            type="string"
            )
        ),
        @OA\Response(
            response=200,
            description="Success",
            @OA\MediaType(
            mediaType="application/json",
            )
        ),
        @OA\Response(
            response=401,
            description="Unauthorized"
        ),
        @OA\Response(
            response=400,
            description="Invalid request"
        ),
        @OA\Response(
            response=404,
            description="not found"
        ),
    )
     */
    /**
     * User logout
     *
     * @param  LogoutRequest $request
     * @return JsonResponse
     */
    public function logout(LogoutRequest $request): JsonResponse
    {
        $result = $this->authService->logoutUser($request);

        return $result ?
            $this->sendResponse([], Response::HTTP_OK) :
            $this->sendResponse([], Response::HTTP_BAD_REQUEST, __('exceptions.invalid_request'));
    }
}
