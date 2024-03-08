<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Bus\Domain;

interface CommandInterface
{
    public function getPayload(): array;
}
