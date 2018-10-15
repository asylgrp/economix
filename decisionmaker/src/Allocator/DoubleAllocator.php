<?php

namespace asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\PayoutRequestCollection;
use byrokrat\amount\Amount;

/**
 * Allocator wrapping two internal allocators
 */
final class DoubleAllocator implements AllocatorInterface
{
    /**
     * @var AllocatorInterface
     */
    private $allocA;

    /**
     * @var AllocatorInterface
     */
    private $allocB;

    public function __construct(AllocatorInterface $allocA, AllocatorInterface $allocB)
    {
        $this->allocA = $allocA;
        $this->allocB = $allocB;
    }

    public function allocate(Amount $availableFunds, PayoutRequestCollection $payouts): PayoutRequestCollection
    {
        $allocated = $this->allocA->allocate($availableFunds, $payouts);

        return $this->allocB->allocate(
            $availableFunds->subtract($allocated->getGrantedAmount()),
            $allocated
        );
    }
}
