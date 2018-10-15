<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Grant;

use byrokrat\amount\Amount;

/**
 * Grant implementation of the grant interface
 */
final class Grant implements GrantInterface
{
    /**
     * @var GrantInterface
     */
    private $decorated;

    /**
     * @var GrantItem
     */
    private $grantItem;

    public function __construct(GrantInterface $decorated, Amount $amount, string $description)
    {
        $this->decorated = $decorated;

        if ($amount->isGreaterThan($decorated->getNotGrantedAmount())) {
            $amount = $decorated->getNotGrantedAmount();
        }

        $this->grantItem = new GrantItem($amount, $description);
    }

    public function getClaimDate(): \DateTimeImmutable
    {
        return $this->decorated->getClaimDate();
    }

    public function getClaimedAmount(): Amount
    {
        return $this->decorated->getClaimedAmount();
    }

    public function getClaimDescription(): string
    {
        return $this->decorated->getClaimDescription();
    }

    public function getGrantedAmount(): Amount
    {
        return $this->decorated->getGrantedAmount()->add($this->grantItem->getGrantedAmount());
    }

    public function getNotGrantedAmount(): Amount
    {
        return $this->getClaimedAmount()->subtract($this->getGrantedAmount());
    }

    public function getGrantItems(): \Generator
    {
        yield from $this->decorated->getGrantItems();
        yield $this->grantItem;
    }
}
