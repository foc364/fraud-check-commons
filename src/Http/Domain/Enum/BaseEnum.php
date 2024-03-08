<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Http\Domain\Enum;

abstract class BaseEnum
{
    public static function getStrings(): array
    {
        return array_values(
            (new \ReflectionClass(get_called_class()))->getConstants()
        );
    }
}
