<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker;

use asylgrp\matchmaker\Matcher\MatcherInterface;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\MatchCollectionInterface;
use asylgrp\matchmaker\Match\MatchCollection;

/**
 * Match payouts to receipts based on matcher logic
 */
class MatchMaker
{
    /**
     * @var MatcherInterface[]
     */
    private $matchers;

    public function __construct(MatcherInterface ...$matchers)
    {
        $this->matchers = $matchers;
    }

    /**
     * Apply matchers to matchables and get the set of found matches
     */
    public function match(MatchableInterface ...$toMatch): MatchCollectionInterface
    {
        $matches = [];
        $notMatched = [];

        foreach ($this->matchers as $matcher) {
            while ($matchable = array_shift($toMatch)) {
                $match = $matcher->match($matchable, array_merge($notMatched, $toMatch));

                if (!$match) {
                    array_push($notMatched, $matchable);
                } else {
                    $matches[] = $match;

                    // matched items should not be tested again
                    foreach ($match->getMatched() as $matched) {
                        self::removeFromArray($matched, $toMatch);
                        self::removeFromArray($matched, $notMatched);
                    }
                }
            }

            // reset for next matcher
            $toMatch = $notMatched;
            $notMatched = [];
        }

        return new MatchCollection(...$matches);
    }

    private static function removeFromArray(MatchableInterface $matched, array &$matchables): void
    {
        if (($key = array_search($matched, $matchables, true)) !== false) {
            unset($matchables[$key]);
        }
    }
}
