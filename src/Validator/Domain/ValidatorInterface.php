<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Validator\Domain;

interface ValidatorInterface
{
    /**
     * @throws ValidationException
     */
    public function validate(array $request): bool;

    public function rules(): array;
}
