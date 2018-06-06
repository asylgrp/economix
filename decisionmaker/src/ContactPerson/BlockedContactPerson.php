<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\ContactPerson;

/**
 * A blocked contact person can temporarily not receive payouts
 */
class BlockedContactPerson extends ActiveContactPerson
{
    public function isBlocked(): bool
    {
        return true;
    }
}
