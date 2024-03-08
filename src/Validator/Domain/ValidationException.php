<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Validator\Domain;

use Exception;
use Throwable;

class ValidationException extends Exception
{
    public function __construct(
        string $message = '',
        private array $errors = [],
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
