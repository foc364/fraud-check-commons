<?php

declare(strict_types=1);

namespace Test\Unit\EventSourcing\Infrastructure\Message;

use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreRepository;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventTypeFormatted;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\MessageRepository;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use PHPUnit\Framework\TestCase;

class MessageRepositoryTest extends TestCase
{
    public function testRetrieveAll(): void
    {
        $eventStoreRepository = $this->createMock(EventStoreRepository::class);
        $messageSerializer = $this->createMock(MessageSerializer::class);
        $aggregateRootId = $this->createMock(AggregateRootId::class);
        $eventTypeFormatter = $this->createMock(EventTypeFormatted::class);

        $eventStoreRepository
            ->expects($this->once())
            ->method('findByAggregateRoot')
            ->willReturn(null);

        $messageSerializer
            ->expects($this->exactly(0))
            ->method('unserializePayload');

        $aggregateRootId
            ->expects($this->once())
            ->method('toString')
            ->willReturn('doc');

        $retrieveAll = (new class ($eventStoreRepository, $messageSerializer, $eventTypeFormatter) extends MessageRepository{
            public function persist(Message ...$messages): void {}
        })->retrieveAll($aggregateRootId);

        $this->assertEquals(0, $retrieveAll->getReturn());
    }

    public function testRetrieveAllAfterVersion(): void
    {
        $eventStoreRepository = $this->createMock(EventStoreRepository::class);
        $messageSerializer = $this->createMock(MessageSerializer::class);
        $aggregateRootId = $this->createMock(AggregateRootId::class);
        $eventTypeFormatter = $this->createMock(EventTypeFormatted::class);

        $eventStoreRepository
            ->expects($this->once())
            ->method('findByAggregateRootAndVersionGreaterThan')
            ->willReturn(null);

        $messageSerializer
            ->expects($this->exactly(0))
            ->method('unserializePayload');

        $aggregateRootId
            ->expects($this->once())
            ->method('toString')
            ->willReturn('doc');

        $retrieveAll = (new class ($eventStoreRepository, $messageSerializer, $eventTypeFormatter) extends MessageRepository{
            public function persist(Message ...$messages): void {}
        })->retrieveAllAfterVersion($aggregateRootId, 1);

        $this->assertEquals(0, $retrieveAll->getReturn());
    }
}
