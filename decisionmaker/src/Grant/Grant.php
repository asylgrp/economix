<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Grant;

use Money\Money;

/**
 * Grant implementation of the grant interface
 */
final class Grant implements GrantInterface
{
    private GrantInterface $decorated;
    private GrantItem $grantItem;

    public function __construct(GrantInterface $decorated, Money $amount, string $description)
    {
        $this->decorated = $decorated;

        if ($amount->greaterThan($decorated->getNotGrantedAmount())) {
            $amount = $decorated->getNotGrantedAmount();
        }

        $this->grantItem = new GrantItem($amount, $description);
    }

    public function getClaimDate(): \DateTimeImmutable
    {
        return $this->decorated->getClaimDate();
    }

    public function getClaimedAmount(): Money
    {
        return $this->decorated->getClaimedAmount();
    }

    public function getClaimDescription(): string
    {
        return $this->decorated->getClaimDescription();
    }

    public function getGrantedAmount(): Money
    {
        return $this->decorated->getGrantedAmount()->add($this->grantItem->getGrantedAmount());
    }

    public function getNotGrantedAmount(): Money
    {
        return $this->getClaimedAmount()->subtract($this->getGrantedAmount());
    }

    public function getGrantItems(): \Generator
    {
        yield from $this->decorated->getGrantItems();
        yield $this->grantItem;
    }
}
