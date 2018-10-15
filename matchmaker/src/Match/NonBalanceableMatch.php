<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Match;

/**
 * A match that can NOT balanced programmatically
 */
final class NonBalanceableMatch extends AbstractMatch
{
    public function isBalanceable(): bool
    {
        return false;
    }
}
