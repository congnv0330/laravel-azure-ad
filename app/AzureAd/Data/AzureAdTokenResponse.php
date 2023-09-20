<?php

namespace App\AzureAd\Data;

readonly class AzureAdTokenResponse
{
    public function __construct(
        public int $expiresIn,
        public string $accessToken,
        public string $refreshToken,
    ) {
    }
}
