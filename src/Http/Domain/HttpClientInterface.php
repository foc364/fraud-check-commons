<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Http\Domain;

interface HttpClientInterface
{
    public function get(array $payload = [], array $options = []): array;

    public function post(array $payload, array $options = []): array;

    public function put(array $payload, array $options = []): array;

    public function patch(array $payload, array $options = []): array;
}
