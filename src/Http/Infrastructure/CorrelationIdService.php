<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Http\Infrastructure;

use PicPay\FraudCheckCommons\Context\Infraestructure\ContextManager;
use Ramsey\Uuid\Uuid;

class CorrelationIdService
{
    public function __construct(private ContextManager $contextManager)
    {
    }

    private const CONTEXT_PATH = 'correlation_id';

    public function get(): ?string
    {
        return $this->contextManager->find(self::CONTEXT_PATH);
    }

    public function set(?string $correlationId): void
    {
        if (empty($correlationId)) {
            $correlationId = $this->generateRequestId();
        }

        $this->contextManager->store(self::CONTEXT_PATH, $correlationId);
    }

    public function getAsHeader(): array
    {
        return [
            'headers' => [
                'X-Request-ID' => $this->get()
            ]
        ];
    }

    private function generateRequestId(): string
    {
        return Uuid::uuid4()->toString();
    }
}
