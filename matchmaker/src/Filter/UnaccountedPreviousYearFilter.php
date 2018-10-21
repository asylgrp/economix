<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Filter;

use asylgrp\matchmaker\Match\MatchCollectionInterface;

final class UnaccountedPreviousYearFilter implements FilterInterface
{
    public function evaluate(MatchCollectionInterface $matches): ResultInterface
    {
        foreach ($matches->getFailures() as $match) {
            foreach ($match->getMatched() as $matched) {
                if ($matched->getAmount()->isPositive() && $matched->getId() == '0') {
                    return new Success('Oredovisad summa från föregående år');
                }
            }
        }

        return new Failure('Ingen oredovisad summa från föregående år');
    }
}
