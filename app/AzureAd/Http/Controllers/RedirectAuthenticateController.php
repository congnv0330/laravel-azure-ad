<?php

namespace App\AzureAd\Http\Controllers;

use App\AzureAd\AzureAdAuthClient;
use App\AzureAd\AzureAdConstant;
use Illuminate\Http\Request;

class RedirectAuthenticateController
{
    public function __construct(
        protected AzureAdAuthClient $azureAdAuthClient,
    ) {
    }

    public function index(Request $request)
    {
        $response = $this->azureAdAuthClient->generateAuthUrl();

        $request->session()->put(AzureAdConstant::CODE_VERIFIER, $response->codeVerifier);

        return redirect($response->url);
    }
}
