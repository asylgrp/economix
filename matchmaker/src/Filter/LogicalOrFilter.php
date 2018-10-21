<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Filter;

use asylgrp\matchmaker\Match\MatchCollectionInterface;

final class LogicalOrFilter implements FilterInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters;

    public function __construct(FilterInterface ...$filters)
    {
        $this->filters = $filters;
    }

    public function evaluate(MatchCollectionInterface $matches): ResultInterface
    {
        $failureMessage = '';

        foreach ($this->filters as $filter) {
            $result = $filter->evaluate($matches);

            if ($result->isSuccess()) {
                return $result;
            }

            $failureMessage .= $result->getMessage() . "\n";
        }

        return new Failure(trim($failureMessage));
    }
}
