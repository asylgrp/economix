<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Filter;

use asylgrp\matchmaker\Match\MatchCollectionInterface;
use Money\Money;

final class UnaccountedAmountFilter implements FilterInterface
{
    private Money $limit;

    public function __construct(Money $limit)
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

        if ($amount && $amount->greaterThan($this->limit)) {
            return new Success(
                sprintf(
                    'Oredovisad totalsumma %s (större än %s)',
                    $amount->getAmount(),
                    $this->limit->getAmount()
                )
            );
        }

        return new Failure(
            sprintf(
                'Oredovisad totalsumma mindre än %s',
                $this->limit->getAmount()
            )
        );
    }
}
