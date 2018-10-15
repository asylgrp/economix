<?php

namespace asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\PayoutRequestCollection;
use byrokrat\amount\Amount;

/**
 * Empty allocator implementation
 */
final class NullAllocator implements AllocatorInterface
{
    public function allocate(Amount $availableFunds, PayoutRequestCollection $payouts): PayoutRequestCollection
    {
        return $payouts;
    }
}
