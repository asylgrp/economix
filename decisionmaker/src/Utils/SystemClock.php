<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Utils;

/**
 * Manage creation of datetimes
 */
class SystemClock
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable;
    }
}
