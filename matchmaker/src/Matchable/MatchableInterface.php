<?php

namespace asylgrp\matchmaker\Matchable;

use Money\Money;

/**
 * A matchable item
 */
interface MatchableInterface
{
    /**
     * Get matchable id
     */
    public function getId(): string;

    /**
     * Get a freetext description
     */
    public function getDescription(): string;

    /**
     * Get ids of related matchables
     *
     * @return string[]
     */
    public function getRelatedIds(): array;

    /**
     * Get amount to match
     */
    public function getAmount(): Money;

    /**
     * Get date to match
     */
    public function getDate(): \DateTimeImmutable;

    /**
     * Get contained matchables
     *
     * @return MatchableInterface[]
     */
    public function getMatchables(): array;
}
