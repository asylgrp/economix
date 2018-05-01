<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Match;

/**
 * Collection found matches
 */
class MatchCollection
{
    /**
     * @var MatchInterface[]
     */
    private $matches;

    public function __construct(MatchInterface ...$matches)
    {
        $this->matches = $matches;
    }

    /**
     * @return MatchInterface[]
     */
    public function getMatches(): array
    {
        return $this->matches;
    }
}
