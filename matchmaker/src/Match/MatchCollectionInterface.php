<?php

namespace asylgrp\matchmaker\Match;

/**
 * @extends \IteratorAggregate<MatchInterface>
 */
interface MatchCollectionInterface extends \IteratorAggregate
{
    /**
     * @return array<MatchInterface>
     */
    public function getMatches(): array;

    /**
     * @return \Generator<MatchInterface>
     */
    public function getIterator(): \Generator;

    /**
     * @return \Generator<MatchInterface>
     */
    public function getSuccessful(): \Generator;

    /**
     * @return \Generator<MatchInterface>
     */
    public function getFailures(): \Generator;

    /**
     * @return \Generator<MatchInterface>
     */
    public function getBalanceables(): \Generator;
}
