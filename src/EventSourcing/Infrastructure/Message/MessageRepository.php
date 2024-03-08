<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message;

use EventSauce\EventSourcing\PaginationCursor;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreRepository;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventTypeFormatted;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageRepository as EventSauceMessageRepository;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Generator;

abstract class MessageRepository implements EventSauceMessageRepository
{
    public function __construct(
        protected EventStoreRepository $eventStoreRepository,
        protected MessageSerializer $messageSerializer,
        protected EventTypeFormatted $eventTypeFormatted
    ) {
    }

    abstract public function persist(Message ...$messages): void;

    public function retrieveAll(AggregateRootId $id): Generator
    {
        $messages = $this->eventStoreRepository->findByAggregateRoot($id->toString());
        $message = null;

        if ($messages === null) {
            return 0;
        }

        foreach ($messages as $retrievedMessage) {
            $payload = $retrievedMessage['payload'];

            if (is_string($payload)) {
                $payload = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
            }

            yield $message = $this->messageSerializer->unserializePayload($payload);
        }

        return $message instanceof Message ? $message->aggregateVersion() : 0;
    }

    public function retrieveAllAfterVersion(AggregateRootId $id, int $aggregateRootVersion): Generator
    {
        $messages = $this->eventStoreRepository->findByAggregateRootAndVersionGreaterThan(
            $id->toString(),
            $aggregateRootVersion
        );
        $message = null;

        if ($messages === null) {
            return 0;
        }

        foreach ($messages as $retrievedMessage) {
            $payload = $retrievedMessage['payload'];

            if (is_string($payload)) {
                $payload = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
            }

            yield $message = $this->messageSerializer->unserializePayload($payload);
        }

        return $message instanceof Message ? $message->aggregateVersion() : 0;
    }

    public function paginate(PaginationCursor $cursor): Generator
    {
        throw new \Exception('pagination not implemented');
    }
}