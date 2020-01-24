<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker;

use Money\Money;

/**
 * @implements \IteratorAggregate<PayoutRequest>
 */
class PayoutRequestCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var array<PayoutRequest>
     */
    private array $payouts;

    /**
     * @param array<PayoutRequest> $payouts
     */
    public function __construct(array $payouts)
    {
        $this->payouts = $payouts;
    }

    /**
     * Get loaded payout requests
     *
     * @return iterable<PayoutRequest>
     */
    public function getIterator(): iterable
    {
        return new \ArrayIterator($this->payouts);
    }

    /**
     * Count loaded payout requests
     */
    public function count(): int
    {
        return count($this->payouts);
    }

    /**
     * Get the summary of claims
     */
    public function getClaimedAmount(): Money
    {
        return $this->summarizeGrantsOn('getClaimedAmount');
    }

    /**
     * Get the summary of granted claims
     */
    public function getGrantedAmount(): Money
    {
        return $this->summarizeGrantsOn('getGrantedAmount');
    }

    /**
     * Get the summary of not granted claims
     */
    public function getNotGrantedAmount(): Money
    {
        return $this->summarizeGrantsOn('getNotGrantedAmount');
    }

    /**
     * Internal grant summary method
     */
    private function summarizeGrantsOn(string $method): Money
    {
        return array_reduce(
            $this->payouts,
            function ($amount, PayoutRequest $payout) use ($method) {
                return $amount ? $amount->add($payout->getGrant()->$method()) : $payout->getGrant()->$method();
            }
        );
    }
}
