<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Observability\Metrics\Aspect;

use Hyperf\Di\Aop\AroundInterface;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Metric\Metric;
use LeoCarmo\CircuitBreaker\CircuitBreaker;

/** @Aspect */
class CircuitBreakerMetricAspect implements AroundInterface
{
    public array $classes = [CircuitBreaker::class . '::isAvailable'];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /** @var CircuitBreaker $instance */
        $instance = $proceedingJoinPoint->getInstance();
        $isAvailable = $proceedingJoinPoint->process();

        if ($isAvailable === false) {
            $labels = [
                'service' => $instance->getService(),
                'status' => 'open',
            ];

            Metric::count('circuit_breaker', 1, $labels);
        }

        return $isAvailable;
    }
}
