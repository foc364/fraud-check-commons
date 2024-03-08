<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\MongoDb;

use Carbon\Carbon;
use EventSauce\EventSourcing\MessageRepository as EventSauceMessageRepository;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\AggregateRootId;
use MongoDB\BSON\UTCDateTime;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\MessageRepository;
use Ramsey\Uuid\Uuid;

final class MongoDbMessageRepository extends MessageRepository implements EventSauceMessageRepository
{
    public function persist(Message ...$messages): void
    {
        foreach ($messages as $message) {
            $aggregateRootId = $message->header(Header::AGGREGATE_ROOT_ID);
            assert($aggregateRootId instanceof AggregateRootId);

            $timestampRecordedAt = Carbon::parse($message->header(Header::TIME_OF_RECORDING))->timestamp;
            $utcDatetime = new UTCDateTime($timestampRecordedAt * 1000);

            $this->eventStoreRepository->insert([
                'event_id' => $message->header(Header::EVENT_ID) ?? Uuid::uuid4()->toString(),
                'event_type' => $this->eventTypeFormatted->format($message->header(Header::EVENT_TYPE)),
                'aggregate_root_id' => $aggregateRootId->toString(),
                'recorded_at' => $message->header(Header::TIME_OF_RECORDING),
                'timestamp_recorded_at' => $timestampRecordedAt,
                'version' => $message->header(Header::AGGREGATE_ROOT_VERSION),
                'payload' => $this->messageSerializer->serializeMessage($message),
                'utc_datetime' => $utcDatetime
            ]);
        }
    }
}