<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Http\Request;

class PassportHelper
{
    /**
     * Method to create token for the user.
     *
     * @param string $email
     * @param string $password
     * @return mixed
     */
    public static function getAccessToken(string $email, string $password)
    {
        $params = [
            'grant_type' => 'password',
            'client_id' => request()->header('client-id'),
            'client_secret' => request()->header('client-secret'),
            'username' => $email,
            'password' => $password,
            'scope' => '*',
        ];

        $requestNew = Request::create('oauth/token', 'POST', $params);
        $response = app()->handle($requestNew)->getContent();

        return json_decode((string)$response, true);
    }

    /**
     * Method to create refresh token for the user.
     *
     * @return mixed
     */
    public static function getRefreshToken()
    {
        $params = [
            'grant_type' => 'refresh_token',
            'client_id' => request()->header('client-id'),
            'client_secret' => request()->header('client-secret'),
            'refresh_token' => request()->header('refresh-token'),
            'scope' => '*',
        ];

        $requestNew = Request::create('oauth/token', 'POST', $params);
        $response = app()->handle($requestNew)->getContent();

        return json_decode((string)$response, true);
    }
}
