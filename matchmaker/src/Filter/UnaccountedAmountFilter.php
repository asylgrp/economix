<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Filter;

use asylgrp\matchmaker\Match\MatchCollectionInterface;
use byrokrat\amount\Amount;

final class UnaccountedAmountFilter implements FilterInterface
{
    /**
     * @var Amount
     */
    private $limit;

    public function __construct(Amount $limit)
    {
        $this->limit = $limit;
    }

    public function evaluate(MatchCollectionInterface $matches): ResultInterface
    {
        $amount = null;

        foreach ($matches->getFailures() as $match) {
            foreach ($match->getMatched() as $matched) {
                $amount = $amount ? $amount->add($matched->getAmount()) : $matched->getAmount();
            }
        }

        if ($amount && $amount->isGreaterThan($this->limit)) {
            return new Success(
                sprintf(
                    'Oredovisad totalsumma %s (större än %s)',
                    $amount,
                    $this->limit
                )
            );
        }

        return new Failure(
            sprintf(
                'Oredovisad totalsumma mindre än %s',
                $this->limit
            )
        );
    }
}
