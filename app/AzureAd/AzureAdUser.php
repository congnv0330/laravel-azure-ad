<?php

namespace App\AzureAd;

use Illuminate\Contracts\Auth\Authenticatable;

readonly class AzureAdUser implements Authenticatable
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
    ) {
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): string
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    public function getAuthPassword(): string
    {
        return '';
    }

    public function getRememberToken(): string
    {
        return '';
    }

    public function setRememberToken($value): void
    {
        //
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}
