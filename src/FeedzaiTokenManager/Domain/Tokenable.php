<?php

namespace PicPay\FraudCheckCommons\FeedzaiTokenManager\Domain;

interface Tokenable
{
    public function getTokenAuthorizationHeader(): string;
}
