<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\PostgreSql;

use Carbon\Carbon;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageRepository as EventSauceMessageRepository;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\MessageRepository;
use Ramsey\Uuid\Uuid;

final class PostgreSqlMessageRepository extends MessageRepository implements EventSauceMessageRepository
{
    public function persist(Message ...$messages): void
    {
        foreach ($messages as $message) {
            $aggregateRootId = $message->header(Header::AGGREGATE_ROOT_ID);
            assert($aggregateRootId instanceof AggregateRootId);

            $this->eventStoreRepository->insert([
                'event_id' => $message->header(Header::EVENT_ID) ?? Uuid::uuid4()->toString(),
                'event_type' => $this->eventTypeFormatted->format($message->header(Header::EVENT_TYPE)),
                'aggregate_root_id' => $aggregateRootId->toString(),
                'recorded_at' => $message->header(Header::TIME_OF_RECORDING),
                'timestamp_recorded_at' => Carbon::parse($message->header(Header::TIME_OF_RECORDING))->timestamp,
                'version' => $message->header(Header::AGGREGATE_ROOT_VERSION),
                'payload' => $this->getMessagePayload($message),
            ]);
        }
    }

    private function getMessagePayload(Message $message): string|array
    {
        return json_encode($this->messageSerializer->serializeMessage($message));
    }
}
