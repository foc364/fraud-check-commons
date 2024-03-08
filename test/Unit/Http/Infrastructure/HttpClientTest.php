<?php

declare(strict_types=1);

namespace Test\Unit\Http\Infrastructure;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use LeoCarmo\CircuitBreaker\CircuitBreakerException;
use PicPay\FraudCheckCommons\Http\Domain\Enum\HttpStatusCode;
use PicPay\FraudCheckCommons\Http\Infrastructure\CorrelationIdService;
use PicPay\FraudCheckCommons\Http\Infrastructure\RequestFailureException;
use PicPay\Hyperf\Commons\Http\Guzzle\GuzzleFactory;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Test\Unit\Http\Infrastructure\Fixtures\FooBarRequester;

class HttpClientTest extends TestCase
{
    /**
     * @var GuzzleFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private GuzzleFactory $factory;

    /**
     * @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private LoggerInterface $logger;

    /**
     * @var CorrelationIdService|\PHPUnit\Framework\MockObject\MockObject
     */
    private CorrelationIdService $correlationIdService;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->createMock(GuzzleFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->correlationIdService = $this->createMock(CorrelationIdService::class);
    }

    /**
     * @dataProvider responseSuccessDataProvider
     */
    public function testResponseSuccess(int $statusCode, string $method, string $json, array $payload, array $expected): void
    {
        $mock = new MockHandler([
            new Response($statusCode, ['X-Foo' => 'Bar'], $json)
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->factory
            ->expects($this->once())
            ->method('get')
            ->willReturn($client);

        $requester = new FooBarRequester($this->factory, $this->logger, $this->correlationIdService);

        $this->assertEquals($expected, $requester->$method($payload));
    }

    /**
     * @dataProvider responseErrorDataProvider
     */
    public function testResponseError(int $statusCode, string $message): void
    {
        $mock = new MockHandler([
            new Response($statusCode, ['X-Foo' => 'Bar'], $message)
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->factory
            ->expects($this->once())
            ->method('get')
            ->willReturn($client);

        $requester = new FooBarRequester($this->factory, $this->logger, $this->correlationIdService);

        $this->expectException(RequestFailureException::class);
        $this->expectExceptionMessageMatches($message);

        $requester->get();
    }

    public function testCircuitBreaker(): void
    {
        $client = $this->createMock(Client::class);

        $client
            ->expects($this->once())
            ->method('request')
            ->willThrowException(new CircuitBreakerException());

        $this->factory
            ->expects($this->once())
            ->method('get')
            ->willReturn($client);

        $this->logger
            ->expects($this->once())
            ->method('error');

        $requester = new FooBarRequester($this->factory, $this->logger, $this->correlationIdService);

        $this->expectException(CircuitBreakerException::class);

        $requester->get();
    }

    public function responseSuccessDataProvider(): iterable
    {
        yield 'Get method' => [
            HttpStatusCode::OK,
            'get',
            '{"get_key": "get_value"}',
            'payload' => [],
            'expected' => [
                'get_key' => 'get_value'
            ]
        ];

        yield 'Post method' => [
            HttpStatusCode::OK,
            'post',
            '{"post_key": "post_value"}',
            'payload' => [
                'foo' => 'bar'
            ],
            'expected' => [
                'post_key' => 'post_value'
            ]
        ];

        yield 'Put method' => [
            HttpStatusCode::OK,
            'put',
            '{"put_key": "put_value"}',
            'payload' => [
                'foo' => 'bar'
            ],
            'expected' => [
                'put_key' => 'put_value'
            ]
        ];

        yield 'Patch method' => [
            HttpStatusCode::OK,
            'patch',
            '{"patch_key": "patch_value"}',
            'payload' => [
                'foo' => 'bar'
            ],
            'expected' => [
                'patch_key' => 'patch_value'
            ]
        ];

        yield 'Empty response' => [
            HttpStatusCode::OK,
            'post',
            '',
            'payload' => [
                'foo' => 'bar'
            ],
            'expected' => []
        ];

        yield 'String response' => [
            HttpStatusCode::OK,
            'post',
            'foobar',
            'payload' => [
                'foo' => 'bar'
            ],
            'expected' => []
        ];
    }

    public function responseErrorDataProvider(): iterable
    {
        yield 'Status code 400' => [
            HttpStatusCode::BAD_REQUEST,
            '/400 Bad Request/'
        ];

        yield 'Status code 401' => [
            HttpStatusCode::UNAUTHORIZED,
            '/401 Unauthorized/'
        ];

        yield 'Status code 403' => [
            HttpStatusCode::FORBIDDEN,
            '/403 Forbidden/'
        ];

        yield 'Status code 404' => [
            HttpStatusCode::NOT_FOUND,
            '/404 Not Found/'
        ];

        yield 'Status code 422' => [
            HttpStatusCode::UNPROCESSABLE_ENTITY,
            '/422 Unprocessable Entity/'
        ];

        yield 'Status code 500' => [
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            '/500 Internal Server Error/'
        ];

        yield 'Status code 502' => [
            HttpStatusCode::BAD_GATEWAY,
            '/502 Bad Gateway/'
        ];

        yield 'Status code 504' => [
            HttpStatusCode::GATEWAY_TIMEOUT,
            '/504 Gateway Time-out/'
        ];
    }
}
