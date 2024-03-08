<?php

declare(strict_types=1);

namespace Test\Unit\FeedzaiTokenManager\Infrastructure;

use DateTimeImmutable;
use PicPay\FraudCheckCommons\FeedzaiTokenManager\Infrastructure\FeedzaiTokenManager;
use PHPUnit\Framework\TestCase;
use Hyperf\Cache\Cache;

class FeedzaiTokenManagerTest extends TestCase
{
    /**
     * @var Cache|\PHPUnit\Framework\MockObject\MockObject
     */
    private Cache $cacheRepository;
    private FeedzaiTokenManager $tokenManager;

    public function setUp(): void
    {
        $this->cacheRepository = $this->createMock(Cache::class);
        $this->tokenManager = new FeedzaiTokenManager($this->cacheRepository);

        parent::setUp();
    }

    public function testShouldCreateANewValidToken(): void
    {
        $now = new DateTimeImmutable();
        $expireAt = $now->modify('+1 day');

        $token = $this->tokenManager->create($expireAt);

        $this->assertEquals('JWT', $token->headers()->get('typ'));
        $this->assertEquals('RS512', $token->headers()->get('alg'));
        $this->assertEquals('123456', $token->headers()->get('kid'));

        $this->assertEquals(env('APP_URL'), $token->claims()->get('iss'));
        $this->assertContains(env('APP_URL'), $token->claims()->get('aud'));

        $isExpired = $this->tokenManager->isExpired($token->toString());

        $this->assertFalse($isExpired);
    }

    public function testShouldCreateAnInvalidToken(): void
    {
        $now = new DateTimeImmutable();
        $expireAt = $now->modify('-1 day');

        $token = ($this->tokenManager->create($expireAt))->toString();

        $isExpired = $this->tokenManager->isExpired($token);

        $this->assertTrue($isExpired);
    }

    public function testShouldReturnFormatedToken(): void
    {
        $this->cacheRepository->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $this->cacheRepository->expects($this->once())
            ->method('set');

        $token = $this->tokenManager->getTokenAuthorizationHeader();

        $this->assertStringContainsString('Bearer', $token);
    }

    public function testShouldCreateNewTokenWhenCacheIsEmpty(): void
    {
        $this->cacheRepository->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $this->cacheRepository->expects($this->once())
            ->method('set');

        $token = $this->tokenManager->getToken();
        $isExpired = $this->tokenManager->isExpired($token);

        $this->assertFalse($isExpired);
    }

    public function testShouldCreateNewTokenWhenHasCachedAnExpiredToken(): void
    {
        // Tempo de expiração = 1629128121 = 16 de agosto de 2021 às 12:35:21
        $oldCachedToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzUxMiIsImZvbyI6ImJhciJ9.eyJpc3MiOiJodHRwOi8vZXhhbXBsZS5jb20iLC' .
            'JhdWQiOiJodHRwOi8vZXhhbXBsZS5vcmciLCJqdGkiOiI0ZjFnMjNhMTJhYSIsImlhdCI6MTYyOTEyODEyMS4zODcyMDgsIm5iZiI6MTY' .
            'yOTEyODEyMS4zODcyMDgsImV4cCI6MTYyOTEyODEyMS4zODcyMDgsInVpZCI6MX0.l5BCIwHCx92liz5rLqC71rQZvrDX45sm1jT__8xy' .
            'U9qxcftOUQwNZov0tkX22Uqkmr9gJAb5iL7x2YxBRuSWp_2gSMiEzcJbMCmkzLFdI2orb3_gnQqGurm4RMIwQzTOd7IkVYYGGlcAjuNvH' .
            'KoM4qH3vOFpm_hsAO7pcy2vTEdM5B66g6oiottEQkIZLArxeoun87Xs1ibry1g0VHSpGhzv6KHr8UPEPSirTRwv18RTt6WiJPwV_-J9Ix' .
            'pOVzby6o42QJDwkQj2M1BmlTx_QLD1BK4X8bl6BGUFOOn6wTbWsvHjN5LKxEPQaGzxGxRBfkg-l-Jqtw4VQ6RfCe5Eh8wWqZxeS9rW9no' .
            '1R-Y5MwoG0kE3r-ecQHkrclB1hA5h6uCJRZYidRGiY_EWrhe1oBeqLvy6I5WTlTe7uXjJ8WIjIqhE8v_ulKU2zQ9XncaRhqsfvGLj4CXl' .
            'j6lE2eh7gZrB1w96T0D9c7TWCZljp8TRKMoP3VOlvA6dL8EGfIYWgcostgMrPxRLN5sPLpCsa1AA1hvhnfMyXAyYXclwoZtM-ldYnYhhb' .
            'fe_17DeeC2js8XLX7wBMWhI2UhyndzixS70lZag8pZkkOePPjN1tNp5-88KwlXEiH4kNsXNMzb97pXHw5VAiob3hH70ui2DUUTqxwhg1s' .
            'jGVpIg0Wqq5K8';
        $cachedTokenIsExpired = $this->tokenManager->isExpired($oldCachedToken);
        $this->assertTrue($cachedTokenIsExpired);

        $this->cacheRepository->expects($this->once())
            ->method('get')
            ->willReturn($oldCachedToken);

        $this->cacheRepository->expects($this->once())
            ->method('set');

        $token = $this->tokenManager->getToken();
        $isExpired = $this->tokenManager->isExpired($token);

        $this->assertFalse($isExpired);
        $this->assertNotEquals($oldCachedToken, $token);
    }

    public function testShouldReturnTokenFromCacheWhenNotExpired(): void
    {
        // Tempo de expiração = 1944660917 = 16 de agosto de 2031 às 12:35:17
        $validCachedToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzUxMiIsImZvbyI6ImJhciJ9.eyJpc3MiOiJodHRwOi8vZXhhbXBsZS5jb20i' .
            'LCJhdWQiOiJodHRwOi8vZXhhbXBsZS5vcmciLCJqdGkiOiI0ZjFnMjNhMTJhYSIsImlhdCI6MTk0NDY2MDkxNy4zODcyMDgsIm5iZiI6M' .
            'Tk0NDY2MDkxNy4zODcyMDgsImV4cCI6MTk0NDY2MDkxNy4zODcyMDgsInVpZCI6MX0.OR2T7abF7TujDM_M-qPBAbh9igHd4x3Zv2BQzT' .
            '7SFfwO3m62mY3RQVkFQlC0_BnylrTAl6pqrI_XBhSwi4DujzvDkoRYMc8sML0uny7yN05KS5DJlY0J7ebIfpt2rmzwl5i7SZ-jitnplLq' .
            'Dw1iQhUAf8pCzClCHvaVxbrsgVNX-JwZPOcpCskEtzHnmA7eVu2K1yVPZlY8nVF31LOIgmZ3RcTcfrXISVyQvkm6ZoDuYT_0ZWYwqWKq-' .
            'ioht3t1BzYr-s3O6xkpLa9rRpO_mxvpt1L8iykuD4-_yBdWPxKn-IuOGuleS4v7IaFnMEZ9DbwvRBr3Vlc07OJzZjSKbqRSX6wsfA4Fwb' .
            'd46w0pbpB9QGz10iM8AfyMS9dvkIW-ZFdmp6AbpdvejLfUeSLD4qUtp23reSJvv2UDSou5I1yt3VzYoFFZTnshgXDNUBkT3fctWaX4sTB' .
            'J0JuHfx_u4tBeM9RN-uFEeBtHJQd7MXGGxzV9qjgRmQW1yhMkSjArJ05nGr77LBrllgzKQosTMRnb6DodzAiqFRTYBksIOFIAx_KfczvQ' .
            'LDGPLCkxXheYu9OFYGOETeZltJfOHmGduWjv6GSZR-WyFnJc7o1O6WbF-fhAnGIuJkDYSBwsCTfRQEH7QtnzVY_0K5dQcY19fOpgEpK37' .
            'G8mP4vxVG-_18yE';
        $cachedTokenIsExpired = $this->tokenManager->isExpired($validCachedToken);
        $this->assertFalse($cachedTokenIsExpired);

        $this->cacheRepository->expects($this->once())
            ->method('get')
            ->willReturn($validCachedToken);

        $this->cacheRepository->expects($this->never())
            ->method('set');

        $token = $this->tokenManager->getToken();
        $isExpired = $this->tokenManager->isExpired($token);

        $this->assertFalse($isExpired);
        $this->assertEquals($token, $validCachedToken);
    }
}
