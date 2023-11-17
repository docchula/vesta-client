<?php

use Docchula\VestaClient\VestaClient;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

it('can generate ID token', function () {
    expect(true)->toBeTrue();

    // Create token
    $url = 'https://vesta.example.com';
    $secret = md5(rand());
    $issuer = 'client.example.com';
    $client = new VestaClient($url, $secret, $issuer);
    $email = 'user@example.com';
    $tokenStr = $client->generateApiIdToken($email, [$email], ['student_id', 'title', 'first_name', 'last_name', 'email']);

    // Validate token
    $token = (new Parser(new JoseEncoder()))->parse($tokenStr);
    expect($token)->toBeInstanceOf(UnencryptedToken::class);
    $tokenClaims = $token->claims();
    expect($tokenClaims->get('iss'))->toBe($issuer);
    expect($tokenClaims->get('email'))->toBe($email);

    $config = Configuration::forSymmetricSigner(new Sha256(), // HMAC SHA256
        InMemory::plainText($secret));
    expect($config->validator()->validate($token,
        new PermittedFor('Vesta'),
        new IssuedBy($issuer),
        new SignedWith($config->signer(), InMemory::plainText($secret)),
    ))->toBeTrue();
    // Not tested: valid at
});
