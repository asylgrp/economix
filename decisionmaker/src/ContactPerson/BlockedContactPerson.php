<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\ContactPerson;

/**
 * A blocked contact person can temporarily not receive payouts
 */
final class BlockedContactPerson extends AbstractContactPerson
{
    public function isBlocked(): bool
    {
        return true;
    }
}
