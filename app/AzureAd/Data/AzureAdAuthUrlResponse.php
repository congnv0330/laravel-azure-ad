<?php

namespace App\AzureAd\Data;

readonly class AzureAdAuthUrlResponse
{
    public function __construct(
        public string $codeVerifier,
        public string $url,
    ) {
    }
}
