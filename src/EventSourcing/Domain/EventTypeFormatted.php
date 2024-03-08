<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Domain;

interface EventTypeFormatted
{
    public function format(string $eventType): string;
}
