<?php

namespace asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\PayoutRequestCollection;
use byrokrat\amount\Amount;

/**
 * Allocate grants to payout requests
 */
interface AllocatorInterface
{
    /**
     * Allocate money and return a new PayoutRequestCollection with updated grants
     */
    public function allocate(Amount $availableFunds, PayoutRequestCollection $payouts): PayoutRequestCollection;
}
