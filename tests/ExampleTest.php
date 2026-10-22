<?php

use Docchula\VestaClient\VestaClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
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
    $token = (new Parser(new JoseEncoder))->parse($tokenStr);
    expect($token)->toBeInstanceOf(UnencryptedToken::class);
    $tokenClaims = $token->claims();
    expect($tokenClaims->get('iss'))->toBe($issuer);
    expect($tokenClaims->get('email'))->toBe($email);

    $config = Configuration::forSymmetricSigner(new Sha256, // HMAC SHA256
        InMemory::plainText($secret));
    expect($config->validator()->validate($token,
        new PermittedFor('Vesta'),
        new IssuedBy($issuer),
        new SignedWith($config->signer(), InMemory::plainText($secret)),
    ))->toBeTrue();
    // Not tested: valid at
});

it('can retrieve student', function () {
    Http::fake([
        'vesta.example.com/internal/v1/students/*' => Http::response(['student_id' => '6000000030', 'first_name' => 'Somchai']),
    ]);

    $secret = md5(rand());
    $issuer = 'client.example.com';
    $client = new VestaClient('https://vesta.example.com', $secret, $issuer);
    $response = $client->retrieveStudent('6000000030', 'user@example.com', ['student_id', 'first_name']);

    expect($response->successful())->toBeTrue();
    expect($response->json('first_name'))->toBe('Somchai');

    Http::assertSentCount(1);
    Http::assertSent(function (Request $request) use ($secret, $issuer) {
        expect($request->method())->toBe('GET');
        expect($request->url())->toBe('https://vesta.example.com/internal/v1/students/6000000030');
        expect($request->header('Accept'))->toBe(['application/json']);

        // Bearer token must be a valid ID token for the requested student
        expect($request->header('Authorization')[0])->toStartWith('Bearer ');
        $token = (new Parser(new JoseEncoder))->parse(substr($request->header('Authorization')[0], 7));
        $config = Configuration::forSymmetricSigner(new Sha256, InMemory::plainText($secret));
        expect($config->validator()->validate($token,
            new PermittedFor('Vesta'),
            new IssuedBy($issuer),
            new SignedWith($config->signer(), InMemory::plainText($secret)),
        ))->toBeTrue();
        expect($token->claims()->get('targets'))->toBe(['6000000030']);
        expect($token->claims()->get('fields'))->toBe(['student_id', 'first_name']);
        expect($token->claims()->get('email'))->toBe('user@example.com');

        return true;
    });
});
