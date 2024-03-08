<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Aggregate;

use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Snapshotting\SnapshotRepository;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\MessageDispatcher;
use EventSauce\EventSourcing\AggregateRootRepository as RegularRepository;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\EventSourcedAggregateRootRepository;
use EventSauce\EventSourcing\MessageDispatcherChain;
use EventSauce\EventSourcing\Snapshotting\AggregateRootRepositoryWithSnapshotting;
use EventSauce\EventSourcing\Snapshotting\AggregateRootWithSnapshotting;
use EventSauce\EventSourcing\Snapshotting\ConstructingAggregateRootRepositoryWithSnapshotting;
use PicPay\FraudCheckCommons\EventSourcing\Domain\AggregateRootRepositoryWithSnapshottingInterface;
use Hyperf\Contract\ContainerInterface;

abstract class AggregateRootRepository implements AggregateRootRepositoryWithSnapshottingInterface
{
    protected string $aggregateRootClassName = '';
    protected array $consumers = [];

    public function __construct(
        private MessageRepository $messageRepository,
        private SnapshotRepository $snapshotRepository,
        private ContainerInterface $container
    ) {
        if (is_a($this->aggregateRootClassName, AggregateRootWithSnapshotting::class, true) === false) {
            throw new \LogicException(
                'You have to set an aggregate root with snapshotting before the repository can be initialized.'
            );
        }
    }

    public function retrieve(AggregateRootId $aggregateRootId): object
    {
        return $this->snapshotRepository()->retrieve($aggregateRootId);
    }

    public function persist(object $aggregateRoot): void
    {
        $this->snapshotRepository()->persist($aggregateRoot);
    }

    public function persistEvents(AggregateRootId $aggregateRootId, int $aggregateRootVersion, object ...$events): void
    {
        $this->snapshotRepository()->persistEvents($aggregateRootId, $aggregateRootVersion, ...$events);
    }

    public function retrieveFromSnapshot(AggregateRootId $aggregateRootId): object
    {
        return $this->snapshotRepository()->retrieveFromSnapshot($aggregateRootId);
    }

    public function storeSnapshot(AggregateRootWithSnapshotting $aggregateRoot): void
    {
        $this->snapshotRepository()->storeSnapshot($aggregateRoot);
    }

    private function snapshotRepository(): AggregateRootRepositoryWithSnapshotting
    {
        return new ConstructingAggregateRootRepositoryWithSnapshotting(
            $this->aggregateRootClassName,
            $this->messageRepository,
            $this->snapshotRepository,
            $this->regularRepository()
        );
    }

    private function regularRepository(): RegularRepository
    {
        return new EventSourcedAggregateRootRepository(
            $this->aggregateRootClassName,
            $this->messageRepository,
            new MessageDispatcherChain(
                new MessageDispatcher(
                    $this->container,
                    ...$this->consumers
                ),
            )
        );
    }
}
