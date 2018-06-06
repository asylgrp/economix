<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\ContactPerson;

/**
 * A banned contact person is never expected to receive payouts
 */
class BannedContactPerson extends ActiveContactPerson
{
    public function isBanned(): bool
    {
        return true;
    }
}
