<?php

namespace PicPay\FraudCheckCommons\Health\Domain;

interface HealthCheckerInterface
{
    public function readiness(): array;
    public function liveness(): array;
}
