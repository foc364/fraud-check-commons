<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Validator\Application;

use Hyperf\Contract\ValidatorInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use PicPay\FraudCheckCommons\Validator\Domain\ValidationException;
use function assert;

abstract class Validator
{
    public const MESSAGE_VALIDATOR_FAILS = "ValidatorFails";

    protected static array $data;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws ValidationException
     */
    public function validate(array $request): bool
    {
        self::$data = $request;

        $factory = ApplicationContext::getContainer()->get(ValidatorFactoryInterface::class);
        assert($factory instanceof  ValidatorFactoryInterface);

        $validator = $factory->make($request, $this->rules());
        assert($validator instanceof ValidatorInterface);

        if ($validator->fails()) {
            throw new ValidationException(self::MESSAGE_VALIDATOR_FAILS, $validator->errors()->getMessages());
        }

        return true;
    }

    abstract protected function rules(): array;
}
