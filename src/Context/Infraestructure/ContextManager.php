<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Context\Infraestructure;

use Swoole\Coroutine;

class ContextManager
{
    private const INVALID_CID = -1;

    public function store(string $key, mixed $value): void
    {
        $context = Coroutine::getContext();
        $context?->offsetSet($key, $value);
    }

    public function find(string $key): mixed
    {
        $context = Coroutine::getContext();

        if ($context === null) {
            return null;
        }

        if (!$context->offsetExists($key)) {
            $value = $this->parentSearch($key, Coroutine::getCid());

            if ($value) {
                $context->offsetSet($key, $value);
            }

            return $value;
        }

        return $context->offsetGet($key);
    }

    private function parentSearch(string $key, ?int $cid = null): mixed
    {
        if (!$pid = $this->getPid($cid)) {
            return null;
        }

        $context = Coroutine::getContext($pid);

        if ($context === null) {
            return null;
        }

        if (!$context->offsetExists($key)) {
            return $this->parentSearch($key, $pid);
        }

        return $context->offsetGet($key);
    }

    private function getPid(int $cid): ?int
    {
        $pid = Coroutine::getPcid($cid);

        return ($pid && $pid !== self::INVALID_CID) ? $pid : null;
    }
}