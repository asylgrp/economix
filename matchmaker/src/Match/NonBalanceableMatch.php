<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Match;

/**
 * A match that can NOT balanced programmatically
 */
class NonBalanceableMatch extends BalanceableMatch
{
    public function isBalanceable(): bool
    {
        return false;
    }
}
