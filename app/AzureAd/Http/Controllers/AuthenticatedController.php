<?php

namespace App\AzureAd\Http\Controllers;

use App\AzureAd\AzureAdAuthClient;
use App\AzureAd\AzureAdConstant;
use App\AzureAd\Http\Requests\LoginRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class AuthenticatedController
{
    public function __construct(
        protected AzureAdAuthClient $azureAdAuthClient,
    ) {
    }

    public function store(LoginRequest $request)
    {
        $response = $this->azureAdAuthClient->getToken(
            codeVerifier: $request->session()->pull(AzureAdConstant::CODE_VERIFIER),
            code: $request->getCode(),
        );

        return redirect()->route('home')
            ->withCookie(
                cookie(
                    AzureAdConstant::ACCESS_TOKEN_KEY,
                    $response->accessToken,
                    $response->expiresIn / 60,
                )
            )
            ->withCookie(
                cookie(
                    AzureAdConstant::REFRESH_TOKEN_KEY,
                    $response->refreshToken,
                    24 * 60,
                )
            );
    }


    public function update(Request $request)
    {
        $refreshToken = $request->cookie(AzureAdConstant::REFRESH_TOKEN_KEY, null);

        if ($refreshToken === null) {
            throw new AuthenticationException();
        }

        $response = $this->azureAdAuthClient->refreshToken($refreshToken);

        return response()
            ->noContent()
            ->withCookie(
                cookie(
                    AzureAdConstant::ACCESS_TOKEN_KEY,
                    $response->accessToken,
                    $response->expiresIn / 60,
                )
            )
            ->withCookie(
                cookie(
                    AzureAdConstant::REFRESH_TOKEN_KEY,
                    $response->refreshToken,
                    24 * 60,
                )
            );
    }

    public function destroy()
    {
        return response()
            ->noContent()
            ->withoutCookie(AzureAdConstant::ACCESS_TOKEN_KEY)
            ->withoutCookie(AzureAdConstant::REFRESH_TOKEN_KEY);
    }
}
