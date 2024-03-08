<?php

namespace PicPay\FraudCheckCommons\FeedzaiTokenManager\Infrastructure;

use DateTimeImmutable;
use PicPay\FraudCheckCommons\FeedzaiTokenManager\Domain\Tokenable;
use InvalidArgumentException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha512;
use Lcobucci\JWT\Signer\Key\InMemory;
use Hyperf\Cache\Cache;
use Ramsey\Uuid\Uuid;

final class FeedzaiTokenManager implements Tokenable
{
    public const CACHE_KEY = 'feedzai_jwt_token';

    private Configuration $configuration;
    private Cache $cacheRepository;
    private int $tokenTTLMinutes;

    public function __construct(Cache $cacheRepository)
    {
        $this->tokenTTLMinutes = config('feedzai.jwt.token_ttl_minutes');

        $privateKey = $this->decodeBase64Key(config('feedzai.jwt.private_key_base64_encoded'));
        $publicKey = $this->decodeBase64Key(config('feedzai.jwt.public_key_base64_encoded'));

        if (empty($privateKey) || empty($publicKey)) {
            throw new InvalidArgumentException('Invalid Feedzai JWT private/public key');
        }

        $this->configuration = Configuration::forAsymmetricSigner(
            new Sha512(),
            InMemory::plainText($privateKey),
            InMemory::plainText($publicKey)
        );

        $this->cacheRepository = $cacheRepository;
    }

    public function create(DateTimeImmutable $expireAt): \Lcobucci\JWT\Token\Plain
    {
        $now = new DateTimeImmutable();

        $token = $this->configuration->builder()
            // Configures the issuer (iss claim)
            ->issuedBy(config('feedzai.jwt.issuer'))
            // Configures the audience (aud claim)
            ->permittedFor(config('feedzai.jwt.audience'))
            // Configures the id (jti claim)
            ->identifiedBy(Uuid::uuid4()->toString())
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            // Configures the time that the token can be used (nbf claim)
            ->canOnlyBeUsedAfter($now)
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($expireAt)
            // Configures a new header key id
            ->withHeader('kid', config('feedzai.jwt.key_id'))
            // Builds a new token
            ->getToken($this->configuration->signer(), $this->configuration->signingKey());

        return $token;
    }

    public function isExpired(string $token): bool
    {
        $now = new DateTimeImmutable();
        $tokenParsed = $this->configuration->parser()->parse($token);

        return $tokenParsed->isExpired($now);
    }

    public function getToken(): string
    {
        $cachedToken = $this->getTokenFromCache();

        if (empty($cachedToken) || $this->isExpired($cachedToken)) {
            return $this->refreshToken();
        }

        return $cachedToken;
    }

    public function getTokenAuthorizationHeader(): string
    {
        return sprintf('%s %s', 'Bearer', $this->getToken());
    }

    private function refreshToken(): string
    {
        $now = new DateTimeImmutable();
        $expireAt = $this->formatExpireAt($now, $this->tokenTTLMinutes);

        $token = ($this->create($expireAt))->toString();

        $this->putTokenCache($token);

        return $token;
    }

    private function formatExpireAt(DateTimeImmutable $datetime, int $tokenTTLMinutes): DateTimeImmutable
    {
        return $datetime->modify(sprintf('+%d minutes', $tokenTTLMinutes));
    }

    private function getTokenFromCache(): ?string
    {
        $cached = $this->cacheRepository->get(self::CACHE_KEY);

        if ($cached === null) {
            return null;
        }

        return (string) $cached;
    }

    private function putTokenCache(string $token): void
    {
        $this->cacheRepository->set(
            self::CACHE_KEY,
            $token
        );
    }

    private function decodeBase64Key(string $key): string
    {
        return base64_decode($key);
    }
}
