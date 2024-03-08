<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Bus\Domain;

interface ResponseInterface
{
    public function toArray(): array;
}
