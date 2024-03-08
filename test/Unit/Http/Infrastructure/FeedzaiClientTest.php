<?php

declare(strict_types=1);

namespace Test\Unit\Http\Infrastructure;

use PicPay\FraudCheckCommons\Http\Infrastructure\CorrelationIdService;
use PicPay\FraudCheckCommons\Http\Infrastructure\FeedzaiClient;
use PHPUnit\Framework\TestCase;
use PicPay\FraudCheckCommons\FeedzaiTokenManager\Domain\Tokenable;
use PicPay\FraudCheckCommons\Http\Domain\Enum\HttpStatusCode;
use PicPay\FraudCheckCommons\Http\Infrastructure\HeaderOptions;
use PicPay\FraudCheckCommons\Http\Infrastructure\HttpClient;
use PicPay\FraudCheckCommons\Http\Infrastructure\RequestFailureException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PicPay\Hyperf\Commons\Http\Guzzle\GuzzleFactory;
use Psr\Log\LoggerInterface;

class FeedzaiClientTest extends TestCase
{
    /**
     * @var GuzzleFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private GuzzleFactory $factory;

    /**
     * @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private LoggerInterface $logger;

    private Tokenable $tokenManager;

    /**
     * @var CorrelationIdService|\PHPUnit\Framework\MockObject\MockObject
     */
    private CorrelationIdService $correlationIdService;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->createMock(GuzzleFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->tokenManager = $this->createMock(Tokenable::class);
        $this->correlationIdService = $this->createMock(CorrelationIdService::class);

        $this->tokenManager
            ->expects($this->any())
            ->method('getTokenAuthorizationHeader')
            ->willReturn('Bearer a-generated-token');
    }

    /**
     * @dataProvider responseSuccessDataProvider
     */
    public function testResponseSuccess(
        string $method,
        string $clientResponse,
        array $payloadToSend,
        array $expectedBody,
        array $expectedHeaders
    ): void {
        $mockResponse = new Response(
            status: HttpStatusCode::OK,
            body: $clientResponse
        );

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('request')
            ->with(
                $method,
                sprintf('%s/%s', env('FEEDZAI_PULSE_URL'), 'test-uri'),
                $expectedHeaders
            )
            ->willReturn($mockResponse);

        $this->factory
            ->expects($this->once())
            ->method('get')
            ->with('feedzai')
            ->willReturn($clientMock);

        $feedzaiClient = new class ($this->factory, $this->logger, $this->tokenManager, $this->correlationIdService) extends FeedzaiClient {
            protected function endpoint(): string
            {
                return '/test-uri';
            }

            protected function timeout(): float
            {
                return 9.9;
            }
        };

        $response = $feedzaiClient->{$method}($payloadToSend);

        $this->assertEquals($expectedBody, $response);
    }

    public function testPutchOrPatchShouldThrowRequestFailureExceptionWhenIsNotFound(): void
    {
        $this->expectException(RequestFailureException::class);

        $mock = new MockHandler([
            new Response(HttpStatusCode::BAD_REQUEST, [], '{}'),
            new Response(HttpStatusCode::OK, [], '{}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->factory
            ->expects($this->once())
            ->method('get')
            ->willReturn($client);

        $feedzaiClient = new class ($this->factory, $this->logger, $this->tokenManager, $this->correlationIdService) extends FeedzaiClient {
            protected function endpoint(): string
            {
                return 'test-uri';
            }

            protected function timeout(): float
            {
                return 9.9;
            }
        };

        $response = $feedzaiClient->patchOrPutRequest([
            'user_id' => '999'
        ]);
    }

    public function testPutchOrPatchShouldPutWhenPatchThrowException(): void
    {
        $this->expectException(RequestFailureException::class);

        $mock = new MockHandler([
            new Response(HttpStatusCode::NOT_FOUND, [], '{}'),
            new Response(HttpStatusCode::BAD_REQUEST, [], '{}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->factory
            ->expects($this->once())
            ->method('get')
            ->willReturn($client);

        $feedzaiClient = new class ($this->factory, $this->logger, $this->tokenManager, $this->correlationIdService) extends FeedzaiClient {
            protected function endpoint(): string
            {
                return 'test-uri';
            }

            protected function timeout(): float
            {
                return 9.9;
            }
        };

        $response = $feedzaiClient->patchOrPutRequest([
            'user_id' => '999'
        ]);
    }

    public function responseSuccessDataProvider(): iterable
    {
        yield 'GET method' => [
            'method' => HttpClient::METHOD_GET,
            'client_response' => '{"GET_key": "GET_value"}',
            'payload_send' => [],
            'expected_body' => [
                'GET_key' => 'GET_value'
            ],
            'expected_headers' => [
                'headers' => [
                    HeaderOptions::TIMEOUT => 9.9,
                    HeaderOptions::AUTHORIZATION => 'Bearer a-generated-token',
                    HeaderOptions::CONTENT_TYPE => 'application/json',
                ],
                'json' => []
            ]
        ];

        yield 'POST method' => [
            'method' => HttpClient::METHOD_POST,
            'client_response' => '{"POST_key": "POST_value"}',
            'payload_send' => [
                'user_id' => 999
            ],
            'expected_body' => [
                'POST_key' => 'POST_value'
            ],
            'expected_headers' => [
                'headers' => [
                    HeaderOptions::TIMEOUT => 9.9,
                    HeaderOptions::AUTHORIZATION => 'Bearer a-generated-token',
                    HeaderOptions::CONTENT_TYPE => 'application/json',
                ],
                'json' => [
                    'user_id' => 999
                ]
            ]
        ];

        yield 'PUT method' => [
            'method' => HttpClient::METHOD_PUT,
            'client_response' => '{"PUT_key": "PUT_value"}',
            'payload_send' => [
                'user_id' => 999
            ],
            'expected_body' => [
                'PUT_key' => 'PUT_value'
            ],
            'expected_headers' => [
                'headers' => [
                    HeaderOptions::TIMEOUT => 9.9,
                    HeaderOptions::AUTHORIZATION => 'Bearer a-generated-token',
                    HeaderOptions::CONTENT_TYPE => 'application/json',
                ],
                'json' => [
                    'user_id' => 999
                ]
            ]
        ];

        yield 'PATCH method' => [
            'method' => HttpClient::METHOD_PATCH,
            'client_response' => '{"PATCH_key": "PATCH_value"}',
            'payload_send' => [
                'user_id' => 999
            ],
            'expected_body' => [
                'PATCH_key' => 'PATCH_value'
            ],
            'expected_headers' => [
                'headers' => [
                    HeaderOptions::TIMEOUT => 9.9,
                    HeaderOptions::AUTHORIZATION => 'Bearer a-generated-token',
                    HeaderOptions::CONTENT_TYPE => 'application/json',
                ],
                'json' => [
                    'user_id' => 999
                ]
            ]
        ];
    }
}
