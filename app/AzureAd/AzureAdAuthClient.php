<?php

namespace App\AzureAd;

use App\AzureAd\Data\AzureAdAuthUrlResponse;
use App\AzureAd\Data\AzureAdTokenResponse;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AzureAdAuthClient
{
    protected string $authUrl;

    protected string $clientId;

    protected string $clientSecret;

    protected string $tenantId;

    protected string $redirectUri;

    protected array $scope;

    public function __construct(Config $config)
    {
        $azureConfig = $config->get('services.azure_ad');

        $this->clientId = $azureConfig['client_id'];
        $this->tenantId = $azureConfig['tenant_id'];
        $this->clientSecret = $azureConfig['client_secret'];
        $this->redirectUri = route('auth.login');
        $this->scope = $azureConfig['scope'];

        $this->authUrl = 'https://login.microsoftonline.com/'.$azureConfig['tenant_id'].'/oauth2/v2.0';
    }

    public function generateAuthUrl(): AzureAdAuthUrlResponse
    {
        $codeVerifier = Str::random(80);

        $hashed = hash('sha256', $codeVerifier, true);
        $codeChallenge = rtrim(strtr(base64_encode($hashed), '+/', '-_'), '=');

        $url = "$this->authUrl/authorize?".http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'response_mode' => 'query',
            'scope' => $this->getScope(),
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);

        return new AzureAdAuthUrlResponse(
            codeVerifier: $codeVerifier,
            url: $url,
        );
    }

    /**
     * @throws RequestException
     */
    public function getToken(string $codeVerifier, string $code): AzureAdTokenResponse
    {
        $response = Http::asForm()->post("$this->authUrl/token", [
            'code' => $code,
            'client_id' => $this->clientId,
            'scope' => $this->getScope(),
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
            'code_verifier' => $codeVerifier,
            'client_secret' => $this->clientSecret,
        ]);

        $response->throw();

        return $this->mapTokenResponse($response->json());
    }

    /**
     * @throws RequestException
     */
    public function refreshToken(string $refreshToken): AzureAdTokenResponse
    {
        $response = Http::asForm()->post("$this->authUrl/token", [
            'client_id' => $this->clientId,
            'scope' => $this->getScope(),
            'grant_type' => 'refresh_token',
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
        ]);

        $response->throw();

        return $this->mapTokenResponse($response->json());
    }

    protected function mapTokenResponse(array $data): AzureAdTokenResponse
    {
        return new AzureAdTokenResponse(
            expiresIn: $data['expires_in'],
            accessToken: $data['access_token'],
            refreshToken: $data['refresh_token'],
        );
    }

    protected function getScope(): string
    {
        return implode(' ', array_unique(
            array_merge(
                ['offline_access', 'user.read'],
                $this->scope,
            ),
        ));
    }
}
