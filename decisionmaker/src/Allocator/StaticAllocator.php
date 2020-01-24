<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\PayoutRequestCollection;
use asylgrp\decisionmaker\Granter\GranterInterface;
use Money\Money;

/**
 * Allocate funds using a granter loaded at construct
 */
final class StaticAllocator implements AllocatorInterface
{
    private GranterInterface $granter;

    public function __construct(GranterInterface $granter)
    {
        $this->granter = $granter;
    }

    public function allocate(Money $availableFunds, PayoutRequestCollection $oldPayouts): PayoutRequestCollection
    {
        $newPayouts = [];

        foreach ($oldPayouts as $payout) {
            $newPayouts[] = $payout->withGrant(
                $this->granter->grant($payout->getGrant())
            );
        }

        return new PayoutRequestCollection($newPayouts);
    }
}
