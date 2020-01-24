<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\Granter\FixedGranter;
use asylgrp\decisionmaker\Granter\RatioGranter;
use asylgrp\decisionmaker\Granter\FixedGranterFactory;
use asylgrp\decisionmaker\Granter\RatioGranterFactory;
use Money\Money;

/**
 * Facade for creating complex allocators
 */
class AllocatorBuilder
{
    private AllocatorInterface $allocator;

    public function __construct()
    {
        $this->allocator = new NullAllocator;
    }

    public function addLazyFixed(Money $max = null): self
    {
        $this->allocator = new DoubleAllocator(
            $this->allocator,
            new LazyAllocator(new FixedGranterFactory($max))
        );

        return $this;
    }

    public function addLazyRatio(): self
    {
        $this->allocator = new DoubleAllocator(
            $this->allocator,
            new LazyAllocator(new RatioGranterFactory)
        );

        return $this;
    }

    public function addStaticFixed(Money $money): self
    {
        $this->allocator = new DoubleAllocator(
            $this->allocator,
            new StaticAllocator(new FixedGranter($money))
        );

        return $this;
    }

    public function addStaticRatio(float $ratio): self
    {
        $this->allocator = new DoubleAllocator(
            $this->allocator,
            new StaticAllocator(new RatioGranter($ratio))
        );

        return $this;
    }

    public function getAllocator(): AllocatorInterface
    {
        return $this->allocator;
    }
}
