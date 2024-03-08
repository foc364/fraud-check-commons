<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Health\Infrastructure;

use PicPay\FraudCheckCommons\Health\Domain\HealthCheckerInterface;
use PicPay\Hyperf\Commons\Health\LivenessService;

use function array_filter;

class HealthChecker implements HealthCheckerInterface
{
    public function __construct(
        private LivenessService $livenessService
    ) {
    }

    public function readiness(): array
    {
        return [
            'message' => 'Alive and kicking!',
            'time' => date(DATE_ATOM),
        ];
    }

    public function liveness(): array
    {
        $serviceStatus = $this->getServiceStatus();
        $hasServiceDown = $this->hasServiceDown($serviceStatus);

        return [
            'services' => $serviceStatus,
            'status' => $hasServiceDown ? 'DOWN' : 'UP',
            'time' => date(DATE_ATOM),
        ];
    }

    private function getServiceStatus(): array
    {
        $liveness = $this->livenessService->liveness();

        $status = [];
        foreach ($liveness['resources'] as $resourceKey => $services) {
            foreach ($services as $serviceKey => $service) {
                $status[$resourceKey . '_' . $serviceKey] = $service['alive'] ?? null;
            }
        }

        return $status;
    }

    private function hasServiceDown(array $status): bool
    {
        $serviceStatus = array_filter($status, function ($value) {
            return $value === false;
        }, ARRAY_FILTER_USE_BOTH);

        return (bool) $serviceStatus;
    }
}
