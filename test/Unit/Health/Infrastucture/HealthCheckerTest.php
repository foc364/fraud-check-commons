<?php

namespace Test\Unit\Health\Infrastucture;

use PicPay\FraudCheckCommons\Health\Infrastructure\HealthChecker;
use PicPay\Hyperf\Commons\Health\LivenessService;
use PHPUnit\Framework\TestCase;

class HealthCheckerTest extends TestCase
{
    private HealthChecker $healthChecker;

    /**
     * @var LivenessService|\PHPUnit\Framework\MockObject\MockObject
     */
    private LivenessService $livenes;

    protected function setUp(): void
    {
        $this->livenes = $this->createMock(LivenessService::class);
        $this->healthChecker = new HealthChecker($this->livenes);
        parent::setUp();
    }

    public function testShouldGetHealth(): void
    {
        $response = $this->healthChecker->readiness();
        $this->assertEquals("Alive and kicking!", $response['message']);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testShouldGetServiceStatus(array $services): void
    {
        $expectedResult = [
            'services' => [
                'database_default' => true,
                'redis_default' => false
            ],
            'status' => 'DOWN'
        ];

        $this->livenes
            ->expects($this->once())
            ->method("liveness")
            ->willReturn($services);

        $actualResult = $this->healthChecker->liveness();

        $this->assertEquals($expectedResult['services'], $actualResult['services']);
        $this->assertEquals($expectedResult['status'], $actualResult['status']);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testShouldCheckItHasServiceDown(array $services): void
    {
        $this->livenes
            ->expects($this->once())
            ->method("liveness")
            ->willReturn($services);

        $serviceResult = $this->healthChecker->liveness();

        $this->assertEquals($serviceResult['status'], 'DOWN');
    }

    public function dataProvider(): array
    {
        return [[[
            "resources" => [
                "database" => [
                    "default" => [
                        'alive' => true,
                        'host' => 'teste',
                        'error' => '',
                        'duration' => 1
                    ]
                ],
                "redis" => [
                    "default" => [
                        'alive' => false,
                        'host' => 'teste',
                        'error' => '',
                        'duration' => 1
                    ]
                ]
            ]
        ]]];
    }
}
