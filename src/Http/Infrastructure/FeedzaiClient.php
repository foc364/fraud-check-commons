<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Http\Infrastructure;

use PicPay\FraudCheckCommons\FeedzaiTokenManager\Domain\Tokenable;
use PicPay\FraudCheckCommons\Http\Domain\Enum\HttpStatusCode;
use PicPay\Hyperf\Commons\Http\Guzzle\GuzzleFactory;
use Psr\Log\LoggerInterface;

abstract class FeedzaiClient extends HttpClient
{
    public function __construct(
        GuzzleFactory $factory,
        LoggerInterface $logger,
        private Tokenable $tokenManager,
        private CorrelationIdService $correlationIdService
    ) {
        parent::__construct($factory, $logger, $correlationIdService);
    }

    public function getKey(): string
    {
        return 'feedzai';
    }

    public function get(array $payload = [], array $options = []): array
    {
        $options = $this->appendAuthorizationHeader($options);
        return parent::get($payload, $options);
    }

    public function post(array $payload, array $options = []): array
    {
        $options = $this->appendAuthorizationHeader($options);
        return parent::post($payload, $options);
    }

    public function put(array $payload, array $options = []): array
    {
        $options = $this->appendAuthorizationHeader($options);
        return parent::put($payload, $options);
    }

    public function patch(array $payload, array $options = []): array
    {
        $options = $this->appendAuthorizationHeader($options);
        return parent::patch($payload, $options);
    }

    /**
     * @throws RequestFailureException
     */
    public function patchOrPutRequest(array $payload, array $options = []): array
    {
        try {
            return $this->patch($payload, $options);
        } catch (RequestFailureException $exception) {
            if ($exception->getCode() === HttpStatusCode::NOT_FOUND) {
                return $this->put($payload, $options);
            }

            throw $exception;
        }
    }

    private function appendAuthorizationHeader(array $options): array
    {
        return array_merge_recursive(
            [
                'headers' => [
                    HeaderOptions::TIMEOUT => $this->timeout(),
                    HeaderOptions::AUTHORIZATION => $this->tokenManager->getTokenAuthorizationHeader(),
                    HeaderOptions::CONTENT_TYPE => 'application/json'
                ]
            ],
            $options
        );
    }

    protected function uri(): string
    {
        return sprintf('%s%s', config('feedzai.pulse.url'), $this->endpoint());
    }

    abstract protected function timeout(): float;

    abstract protected function endpoint(): string;
}
