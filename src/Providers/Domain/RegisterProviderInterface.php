<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Providers\Domain;

interface RegisterProviderInterface
{
    public static function register(): array;
}
