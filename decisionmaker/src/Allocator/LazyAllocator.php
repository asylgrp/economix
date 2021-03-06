<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\PayoutRequestCollection;
use asylgrp\decisionmaker\Granter\GranterFactoryInterface;
use Money\Money;

/**
 * Create a granter at runtime and allocate funds
 */
final class LazyAllocator implements AllocatorInterface
{
    private GranterFactoryInterface $factory;

    public function __construct(GranterFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function allocate(Money $funds, PayoutRequestCollection $payouts): PayoutRequestCollection
    {
        return (new StaticAllocator($this->factory->createGranter($funds, $payouts)))->allocate($funds, $payouts);
    }
}
