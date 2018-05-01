<?php

namespace asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\MatchInterface;

/**
 * Attempted to match needle with any objects in haystack
 */
interface MatcherInterface
{
    /**
     * @param MatchableInterface[] $haystack
     */
    public function match(MatchableInterface $needle, array $haystack): ?MatchInterface;
}
