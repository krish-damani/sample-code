<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * Class ApiBaseController
 *
 * HTTP codes utilize inbuilt response codes defined in "Symfony\Component\HttpFoundation\Response"
 */
abstract class ApiBaseController extends Controller
{
    /**
     * Send API response.
     *
     * @param  $responseData
     * @param  int $responseCode
     * @param  string $message
     * @param  array $headers
     * @return JsonResponse
     */
    public function sendResponse($responseData, int $responseCode, string $message = 'success', array $headers = []) : JsonResponse
    {
        return response()->json(
            ['data' => $responseData, 'message' => $message],
            $responseCode,
            $headers
        );
    }
}