<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Bus\Domain;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command);
}
