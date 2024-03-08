<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Http\Infrastructure;

use PicPay\FraudCheckCommons\Http\Domain\HttpClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use LeoCarmo\CircuitBreaker\CircuitBreakerException;
use PicPay\Hyperf\Commons\Http\Guzzle\GuzzleFactory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerInterface;

use function is_array;
use function json_decode;
use function array_merge_recursive;
use function sprintf;

abstract class HttpClient implements HttpClientInterface
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';

    private Client $client;

    public function __construct(
        GuzzleFactory $factory,
        private LoggerInterface $logger,
        private CorrelationIdService $correlationIdService
    ) {
        $this->client = $factory->get($this->getKey());
    }

    public function get(array $payload = [], array $options = []): array
    {
        return $this->send(self::METHOD_GET, $payload, $options);
    }

    public function post(array $payload, array $options = []): array
    {
        return $this->send(self::METHOD_POST, $payload, $options);
    }

    public function put(array $payload, array $options = []): array
    {
        return $this->send(self::METHOD_PUT, $payload, $options);
    }

    public function patch(array $payload, array $options = []): array
    {
        return $this->send(self::METHOD_PATCH, $payload, $options);
    }

    abstract protected function getKey(): string;

    abstract protected function uri(): string;

    private function send(string $method, $payload = [], $options = []): array
    {
        try {
            $options = array_merge_recursive(
                [
                    RequestOptions::JSON => $payload
                ],
                $this->correlationIdService->getAsHeader(),
                $options
            );

            $response = $this->client->request($method, $this->uri(), $options);

            $contents = $response->getBody()->getContents();

            if (empty($contents)) {
                return [];
            }

            $decodedContents = json_decode($contents, true);

            if (is_array($decodedContents) === false) {
                return [];
            }

            return $decodedContents;
        } catch (CircuitBreakerException $exception) {
            $this->logCircuitBreakerOpen();

            throw $exception;
        } catch (ClientExceptionInterface $exception) {
            $this->logger->error('request-failure', [
                'method' => $method,
                'uri' => $this->uri(),
                'options' => $options,
                'exception' => $exception
            ]);

            throw new RequestFailureException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    private function logCircuitBreakerOpen(): void
    {
        $this->logger->error(sprintf('%s_circuit_breaker_open', $this->getKey()));
    }
}
