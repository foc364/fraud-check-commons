<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Domain;

use EventSauce\EventSourcing\Snapshotting\AggregateRootRepositoryWithSnapshotting;

interface AggregateRootRepositoryWithSnapshottingInterface extends AggregateRootRepositoryWithSnapshotting
{
}
