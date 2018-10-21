<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Filter;

use asylgrp\matchmaker\Match\MatchCollectionInterface;

final class UnaccountedDateFilter implements FilterInterface
{
    /**
     * @var \DateTimeImmutable
     */
    private $limit;

    public function __construct(\DateTimeImmutable $limit)
    {
        $this->limit = $limit;
    }

    public function evaluate(MatchCollectionInterface $matches): ResultInterface
    {
        $oldestDate = null;

        foreach ($matches->getFailures() as $match) {
            foreach ($match->getMatched() as $matched) {
                if (!$matched->getAmount()->isPositive()) {
                    continue;
                }

                if (!$oldestDate || $matched->getDate() < $oldestDate) {
                    $oldestDate = $matched->getDate();
                }
            }
        }

        if ($oldestDate && $oldestDate < $this->limit) {
            return new Success(
                sprintf(
                    'Oredovisad summa från %s (äldre än %s)',
                    $oldestDate->format('Y-m-d'),
                    $this->limit->format('Y-m-d')
                )
            );
        }

        return new Failure(
            sprintf(
                'Ingen oredovisad summa äldre än %s',
                $this->limit->format('Y-m-d')
            )
        );
    }
}
