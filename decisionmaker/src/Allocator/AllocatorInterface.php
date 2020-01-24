<?php

namespace asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\PayoutRequestCollection;
use Money\Money;

/**
 * Allocate grants to payout requests
 */
interface AllocatorInterface
{
    /**
     * Allocate money and return a new PayoutRequestCollection with updated grants
     */
    public function allocate(Money $availableFunds, PayoutRequestCollection $payouts): PayoutRequestCollection;
}
