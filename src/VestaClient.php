<?php

namespace Docchula\VestaClient;

use DateTimeImmutable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Configuration as JwtConfiguration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class VestaClient
{
    protected PendingRequest $httpClient;

    protected JwtConfiguration $jwtConfig;

    protected string $tokenIssuer;

    public function __construct(?string $url = null, ?string $secret = null, ?string $issuer = null)
    {
        $this->httpClient = new PendingRequest();
        $this->httpClient->baseUrl($url ?? config('vesta-client.url'));
        $this->jwtConfig = JwtConfiguration::forSymmetricSigner(new Sha256(), InMemory::plainText($secret ?? config('vesta-client.secret', '')));
        $this->tokenIssuer = $issuer ?? config('vesta-client.issuer');
    }

    public function isEnabled(): bool
    {
        return (bool) config('vesta-client.secret');
    }

    protected function getJwtBuilder(?string $expireTime = '+2 hour'): Builder
    {
        $now = new DateTimeImmutable();

        return $this->jwtConfig->builder()
            ->issuedBy($this->tokenIssuer)
            ->permittedFor('Vesta')
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now->modify('-1 minute'))
            ->expiresAt($now->modify($expireTime ?? '+2 hour'));
    }

    public function generateApiIdToken(?string $email, array $targets, array $fields, ?string $expireTime = null): string
    {
        $token = $this->getJwtBuilder($expireTime)->withClaim('fields', $fields)->withClaim('targets', $targets);
        if (isset($email)) {
            $token = $token->withClaim('email', $email);
        }

        return $token->getToken($this->jwtConfig->signer(), $this->jwtConfig->signingKey())->toString();
    }

    public function retrieveStudent(string $identifier, ?string $userEmail = null, ?array $fields = null): Response
    {
        return $this->httpClient->acceptJson()
            ->withToken($this->generateApiIdToken($userEmail, [$identifier], $fields ?? ['student_id', 'title', 'first_name', 'last_name', 'email']))
            ->get('/internal/v1/students/'.$identifier);
    }
}
