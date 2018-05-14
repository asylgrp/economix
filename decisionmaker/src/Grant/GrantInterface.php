<?php

namespace asylgrp\decisionmaker\Grant;

use byrokrat\amount\Amount;

/**
 * The decisionmaker grant interface
 */
interface GrantInterface
{
    /**
     * Get date when claim was made
     */
    public function getClaimDate(): \DateTimeImmutable;

    /**
     * Get the amount claimed
     */
    public function getClaimedAmount(): Amount;

    /**
     * Get freetext description of claim
     */
    public function getClaimDescription(): string;

    /**
     * Get the amount granted
     */
    public function getGrantedAmount(): Amount;

    /**
     * Get the amount not granted
     */
    public function getNotGrantedAmount(): Amount;

    /**
     * Get grant specifications
     *
     * @return \Generator & iterable<GrantItem>
     */
    public function getGrantItems(): \Generator;
}
