<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\ContactPerson;

/**
 * A banned contact person is never expected to receive payouts
 */
final class BannedContactPerson extends AbstractContactPerson
{
    public function isBanned(): bool
    {
        return true;
    }
}
