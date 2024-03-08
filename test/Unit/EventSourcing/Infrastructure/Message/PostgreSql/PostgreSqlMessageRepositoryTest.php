<?php

namespace Test\Unit\EventSourcing\Infrastructure\Message\PostgreSql;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use PHPUnit\Framework\TestCase;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreRepository;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventTypeFormatted;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\DomainEvent;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\MongoDb\MongoDbMessageRepository;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\PostgreSql\PostgreSqlMessageRepository;

class PostgreSqlMessageRepositoryTest extends TestCase
{
    public function testShouldPersist()
    {
        $eventStoreRepository = $this->createMock(EventStoreRepository::class);
        $messageSerializer = $this->createMock(MessageSerializer::class);
        $aggregateRootId = $this->createMock(AggregateRootId::class);
        $domainEvent = $this->createMock(DomainEvent::class);
        $eventTypeFormatter = $this->createMock(EventTypeFormatted::class);

        $eventStoreRepository
            ->expects($this->once())
            ->method('insert');

        $messageSerializer
            ->expects($this->once())
            ->method('serializeMessage');

        $aggregateRootId
            ->expects($this->once())
            ->method('toString')
            ->willReturn('doc');

        $eventTypeFormatter
            ->expects($this->once())
            ->method('format')
            ->willReturn('DomainEvent');

        $message = new Message($domainEvent, [
            Header::AGGREGATE_ROOT_ID => $aggregateRootId,
            Header::EVENT_TYPE => 'test.namespace.domain_event',
        ]);

        (new PostgreSqlMessageRepository($eventStoreRepository, $messageSerializer, $eventTypeFormatter))->persist($message);
    }
}
