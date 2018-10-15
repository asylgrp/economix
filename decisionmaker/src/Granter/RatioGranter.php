<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\Grant\GrantInterface;
use asylgrp\decisionmaker\Grant\Grant;
use byrokrat\amount\Rounder\RoundDown;

/**
 * Add a specified ratio of not granted amount to each grant
 */
final class RatioGranter implements GranterInterface
{
    /**
     * @var float
     */
    private $ratio;

    public function __construct(float $ratio)
    {
        if ($ratio > 1.0 || $ratio < 0.0) {
            throw new \LogicException("Invalid ratio $ratio");
        }

        $this->ratio = $ratio;
    }

    public function grant(GrantInterface $grant): GrantInterface
    {
        $amount = $grant->getNotGrantedAmount()->multiplyWith($this->ratio)->roundTo(0, new RoundDown);

        if ($amount->isZero()) {
            return $grant;
        }

        return new Grant(
            $grant,
            $amount,
            "Added ratio: {$grant->getNotGrantedAmount()} * {$this->ratio}"
        );
    }
}
