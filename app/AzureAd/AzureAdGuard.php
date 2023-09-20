<?php

namespace App\AzureAd;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class AzureAdGuard implements Guard
{
    protected ?Authenticatable $user = null;

    public function __construct(
        protected AzureAdGraphClient $azureAdClient,
        protected Request            $request,
    ) {
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function guest(): bool
    {
        return empty($this->request->cookie(AzureAdConstant::ACCESS_TOKEN_KEY))
            && empty($this->request->cookie(AzureAdConstant::REFRESH_TOKEN_KEY));
    }

    public function user(): ?Authenticatable
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = $this->getTokenFromRequest();

        if (! $token) {
            return null;
        }

        $user = $this->azureAdClient->me($token);

        if ($user === false) {
            return null;
        }

        return $this->user = $user;
    }

    public function id()
    {
        return $this->user?->getAuthIdentifier();
    }

    public function validate(array $credentials = []): bool
    {
        return true;
    }

    public function hasUser(): bool
    {
        return $this->user !== null;
    }

    public function setUser(Authenticatable $user): self
    {
        $this->user = $user;

        return $this;
    }

    protected function getTokenFromRequest(): ?string
    {
        return $this->request->cookie(AzureAdConstant::ACCESS_TOKEN_KEY);
    }
}
