<?php

namespace PicPay\FraudCheckCommons\Broker\Domain\ValueObject;

use PicPay\Contracts\Helper\UuidGeneratorInterface;
use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid implements UuidGeneratorInterface
{
    public static function generate(): string
    {
        return RamseyUuid::uuid4()->toString();
    }

    public function uuid4(): string
    {
        return RamseyUuid::uuid4()->toString();
    }

    public function isValid(string $uuid): bool
    {
        return RamseyUuid::isValid($uuid);
    }
}
