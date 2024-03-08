<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Logger\Infrastructure;

use Monolog\Processor\ProcessorInterface;
use PicPay\FraudCheckCommons\Http\Infrastructure\CorrelationIdService;

class CorrelationIdLogProcessor implements ProcessorInterface
{
    private const RECORD_PATH = 'X-Request-ID';

    public function __construct(private CorrelationIdService $correlationIdService)
    {
    }

    public function __invoke(array $record)
    {
        $record[self::RECORD_PATH] = $this->correlationIdService->get();

        return $record;
    }
}