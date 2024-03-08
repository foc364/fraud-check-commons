<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Providers\Infrastructure;

use EventSauce\EventSourcing\ClassNameInflector as ClassNameInflectorInterface;
use EventSauce\EventSourcing\DotSeparatedSnakeCaseInflector;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Hyperf\Contract\StdoutLoggerInterface;
use PicPay\Contracts\Concurrency\ConcurrencyFactoryInterface;
use PicPay\Contracts\Helper\UuidGeneratorInterface;
use PicPay\FraudCheckCommons\Broker\Domain\BrokerProducerInterface;
use PicPay\FraudCheckCommons\Broker\Domain\ValueObject\Uuid;
use PicPay\FraudCheckCommons\Broker\Infrastructure\BrokerProducer;
use PicPay\FraudCheckCommons\Bus\Domain\CommandBusInterface;
use PicPay\FraudCheckCommons\Bus\Infrastructure\Factory\CommandBusFactory;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreRepository as EventStoreRepositoryInterface;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreSnapshotRepository as EventStoreSnapshotRepositoryInterface;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventTypeFormatted as EventTypeFormattedInterface;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\EventStoreRepository;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\EventStoreSnapshotRepository;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\EventTypeFormatted;
use PicPay\FraudCheckCommons\FeedzaiTokenManager\Domain\Tokenable;
use PicPay\FraudCheckCommons\FeedzaiTokenManager\Infrastructure\FeedzaiTokenManager;
use PicPay\FraudCheckCommons\Health\Domain\HealthCheckerInterface;
use PicPay\FraudCheckCommons\Health\Infrastructure\HealthChecker;
use PicPay\FraudCheckCommons\Logger\Infrastructure\Logger;
use PicPay\FraudCheckCommons\Logger\Infrastructure\StdoutLoggerFactory;
use PicPay\FraudCheckCommons\Providers\Domain\RegisterProviderInterface;
use PicPay\Hyperf\Commons\Concurrency\Hyperf\HyperfConcurrencyFactory;
use Psr\Log\LoggerInterface;

class DependencyInjectionProvider implements RegisterProviderInterface
{
    public static function register(): array
    {
        return [
            BrokerProducerInterface::class => BrokerProducer::class,
            ClassNameInflectorInterface::class => DotSeparatedSnakeCaseInflector::class,
            CommandBusInterface::class => CommandBusFactory::class,
            ConcurrencyFactoryInterface::class => HyperfConcurrencyFactory::class,
            EventStoreRepositoryInterface::class => EventStoreRepository::class,
            EventStoreSnapshotRepositoryInterface::class => EventStoreSnapshotRepository::class,
            EventTypeFormattedInterface::class => EventTypeFormatted::class,
            HealthCheckerInterface::class => HealthChecker::class,
            LoggerInterface::class => Logger::class,
            MessageSerializer::class => ConstructingMessageSerializer::class,
            StdoutLoggerInterface::class => StdoutLoggerFactory::class,
            Tokenable::class => FeedzaiTokenManager::class,
            UuidGeneratorInterface::class => Uuid::class
        ];
    }
}
