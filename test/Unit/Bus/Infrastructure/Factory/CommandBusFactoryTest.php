<?php

declare(strict_types=1);

namespace Test\Unit\Bus\Infrastructure\Factory;

use PicPay\FraudCheckCommons\Bus\Domain\CommandBusInterface;
use PicPay\FraudCheckCommons\Bus\Infrastructure\Factory\CommandBusFactory;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Container;
use PHPUnit\Framework\TestCase;

class CommandBusFactoryTest extends TestCase
{
    public function testCreateCommandBus(): void
    {
        $container = $this->createMock(Container::class);
        $config = $this->createMock(ConfigInterface::class);

        $container
            ->expects($this->once())
            ->method('get')
            ->with(ConfigInterface::class)
            ->willReturn($config);

        $config
            ->expects($this->once())
            ->method('get')
            ->with('command-bus')
            ->willReturn([]);

        $this->assertInstanceOf(
            CommandBusInterface::class,
            (new CommandBusFactory())($container)
        );
    }
}
