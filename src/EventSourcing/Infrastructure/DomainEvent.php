<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

interface DomainEvent extends SerializablePayload
{
}
