<?php

namespace PicPay\FraudCheckCommons;

use PicPay\Contracts\Broker\BrokerFactoryInterface;
use PicPay\Contracts\Coroutine\CoroutineInterface;
use PicPay\FraudCheckCommons\Providers\Infrastructure\DependencyInjectionProvider;
use PicPay\Hyperf\Commons\Broker\BrokerFactory;
use PicPay\Hyperf\Commons\Coroutine\Hyperf\HyperfCoroutine;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => array_merge(
                [
                    BrokerFactoryInterface::class => BrokerFactory::class,
                    CoroutineInterface::class => HyperfCoroutine::class
                ],
                DependencyInjectionProvider::register()
            ),
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'databases' => [],
            'listeners' => [],
            'publish' => [
                [
                    'id' => 'config_circuit_breaker',
                    'description' => 'Config file do circuit breaker.', // descrever
                    // Recomenda-se que a configuração padrão seja colocada na pasta de publicação e o nome do arquivo seja igual ao nome do componente
                    'source' => __DIR__ . '/../config/autoload/circuit-breaker.php', // Caminho do arquivo de configuração correspondente
                    'destination' => BASE_PATH . '/config/autoload/circuit-breaker.php', // Copia como o arquivo neste caminho
                ], [
                    'id' => 'config_event_tracking',
                    'description' => 'Config file do envent source.',
                    'source' => __DIR__ . '/../config/autoload/event-tracking.php',
                    'destination' => BASE_PATH . '/config/autoload/event-tracking.php',
                ], [
                    'id' => 'config_feedzai',
                    'description' => 'Config file da feedzai.',
                    'source' => __DIR__ . '/../config/autoload/feedzai.php',
                    'destination' => BASE_PATH . '/config/autoload/feedzai.php',
                ], [
                    'id' => 'config_guzzle',
                    'description' => 'Config file do guzzle.',
                    'source' => __DIR__ . '/../config/autoload/guzzle.php',
                    'destination' => BASE_PATH . '/config/autoload/guzzle.php',
                ], [
                    'id' => 'config_logger',
                    'description' => 'Config file do logger.',
                    'source' => __DIR__ . '/../config/autoload/logger.php',
                    'destination' => BASE_PATH . '/config/autoload/logger.php',
                ], [
                    'id' => 'config_mongo',
                    'description' => 'Config file do mongo.',
                    'source' => __DIR__ . '/../config/autoload/mongodb.php',
                    'destination' => BASE_PATH . '/config/autoload/mongodb.php',
                ], [
                    'id' => 'config_redis',
                    'description' => 'Config file do redis.',
                    'source' => __DIR__ . '/../config/autoload/redis.php',
                    'destination' => BASE_PATH . '/config/autoload/redis.php',
                ], [
                    'id' => 'config_watcher',
                    'description' => 'Config file do watcher.',
                    'source' => __DIR__ . '/../config/autoload/watcher.php',
                    'destination' => BASE_PATH . '/config/autoload/watcher.php',
                ], [
                    'id' => 'config_exceptions',
                    'description' => 'Config file do exceptions.',
                    'source' => __DIR__ . '/../config/autoload/exceptions.php',
                    'destination' => BASE_PATH . '/config/autoload/exceptions.php',
                ],
            ],
            // Você também pode continuar a definir outras configurações, que eventualmente serão mescladas no armazenamento de configuração correspondente ao ConfigInterface
        ];
    }
}
