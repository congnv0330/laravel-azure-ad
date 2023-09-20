<?php

namespace App\AzureAd;

use Illuminate\Support\Facades\Http;

class AzureAdGraphClient
{
    protected string $baseUrl = 'https://graph.microsoft.com/v1.0';

    public function me(string $accessToken): AzureAdUser|false
    {
        $response = Http::withToken($accessToken)->get("{$this->baseUrl}/me");

        if ($response->failed()) {
            return false;
        }

        $user = $response->json();

        return new AzureAdUser(
            id: $user['id'],
            name: $user['displayName'],
            email: $user['mail'],
        );
    }
}
