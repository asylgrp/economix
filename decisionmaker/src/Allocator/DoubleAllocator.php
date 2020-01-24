<?php

namespace asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\PayoutRequestCollection;
use Money\Money;

/**
 * Allocator wrapping two internal allocators
 */
final class DoubleAllocator implements AllocatorInterface
{
    private AllocatorInterface $allocA;
    private AllocatorInterface $allocB;

    public function __construct(AllocatorInterface $allocA, AllocatorInterface $allocB)
    {
        $this->allocA = $allocA;
        $this->allocB = $allocB;
    }

    public function allocate(Money $availableFunds, PayoutRequestCollection $payouts): PayoutRequestCollection
    {
        $allocated = $this->allocA->allocate($availableFunds, $payouts);

        return $this->allocB->allocate(
            $availableFunds->subtract($allocated->getGrantedAmount()),
            $allocated
        );
    }
}
