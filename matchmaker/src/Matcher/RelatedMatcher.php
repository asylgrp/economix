<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\MatchInterface;
use asylgrp\matchmaker\Match\BalanceableMatch;

/**
 * Match items that have been explicitly related
 */
final class RelatedMatcher implements MatcherInterface
{
    public function match(MatchableInterface $needle, array $haystack): ?MatchInterface
    {
        if ($related = $this->getRelated($needle, $haystack)) {
            return new BalanceableMatch($needle, ...array_values($related));
        }

        return null;
    }

    /**
     * Recursively find related items
     *
     * @param  MatchableInterface[] $haystack
     * @return MatchableInterface[]
     */
    private function getRelated(MatchableInterface $needle, array $haystack): array
    {
        $related = [];

        foreach ($haystack as $matchable) {
            if (in_array($matchable->getId(), $needle->getRelatedIds())) {
                $related[$matchable->getId()] = $matchable;

                // drop matched item from haystack
                unset($haystack[array_search($matchable, $haystack, true)]);

                // recursively find more relations
                $related = array_merge($related, $this->getRelated($matchable, $haystack));
            }
        }

        return $related;
    }
}
