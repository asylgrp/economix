<?php

namespace asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\PayoutRequestCollection;
use Money\Money;

/**
 * Empty allocator implementation
 */
final class NullAllocator implements AllocatorInterface
{
    public function allocate(Money $availableFunds, PayoutRequestCollection $payouts): PayoutRequestCollection
    {
        return $payouts;
    }
}
