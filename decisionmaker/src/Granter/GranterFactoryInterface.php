<?php

namespace asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\PayoutRequestCollection;
use byrokrat\amount\Amount;

/**
 * Factory for creating granters
 */
interface GranterFactoryInterface
{
    /**
     * Create granter based on available funds and current payout requests
     */
    public function createGranter(Amount $availableFunds, PayoutRequestCollection $payouts): GranterInterface;
}
