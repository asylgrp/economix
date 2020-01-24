<?php

namespace asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\PayoutRequestCollection;
use Money\Money;

/**
 * Factory for creating granters
 */
interface GranterFactoryInterface
{
    /**
     * Create granter based on available funds and current payout requests
     */
    public function createGranter(Money $availableFunds, PayoutRequestCollection $payouts): GranterInterface;
}
