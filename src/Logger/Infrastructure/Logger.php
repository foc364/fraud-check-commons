<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Logger\Infrastructure;

use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    public function __construct(private StdoutLoggerInterface $logger)
    {
    }

    public function emergency($message, array $context = array())
    {
        $this->logger->emergency($message, $context);
    }

    public function alert($message, array $context = array())
    {
        $this->logger->alert($message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->logger->critical($message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->logger->error($message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->logger->warning($message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->logger->notice($message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->logger->info($message, $context);
    }

    public function debug($message, array $context = array())
    {
        $this->logger->debug($message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        $this->logger->log($level, $message, $context);
    }
}
