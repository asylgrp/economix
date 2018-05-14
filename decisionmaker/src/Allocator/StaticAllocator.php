<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\PayoutRequestCollection;
use asylgrp\decisionmaker\Granter\GranterInterface;
use byrokrat\amount\Amount;

/**
 * Allocate funds using a granter loaded at construct
 */
class StaticAllocator implements AllocatorInterface
{
    /**
     * @var GranterInterface
     */
    private $granter;

    public function __construct(GranterInterface $granter)
    {
        $this->granter = $granter;
    }

    public function allocate(Amount $availableFunds, PayoutRequestCollection $oldPayouts): PayoutRequestCollection
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
