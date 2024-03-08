<?php

declare(strict_types=1);

namespace Test\Unit\Http\Infrastructure\Fixtures;

use PicPay\FraudCheckCommons\Http\Infrastructure\HttpClient;

class FooBarRequester extends HttpClient
{
    protected function getKey(): string
    {
        return 'foo-bar';
    }

    protected function uri(): string
    {
        return '/api/v1/foo-bar';
    }
}
