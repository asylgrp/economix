<?php

namespace asylgrp\matchmaker\Match;

use asylgrp\matchmaker\Matchable\MatchableInterface;

/**
 * A matched set of matchables
 */
interface MatchInterface
{
    /**
     * Get matched set of matchables
     *
     * @return MatchableInterface[]
     */
    public function getMatched(): array;

    /**
     * Check if the matched amounts balance out
     */
    public function isBalanced(): bool;

    /**
     * Check if match can be balanced by adding a matchable
     */
    public function isBalanceable(): bool;

    /**
     * Check if match is successfull (eg. balanced or balanceable)
     */
    public function isSuccess(): bool;
}
