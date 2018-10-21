<?php

namespace asylgrp\matchmaker\Filter;

use asylgrp\matchmaker\Match\MatchCollectionInterface;

interface FilterInterface
{
    /**
     * Evaluate a collection of matches
     */
    public function evaluate(MatchCollectionInterface $matches): ResultInterface;
}
