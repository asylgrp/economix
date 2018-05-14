<?php

namespace asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\Grant\GrantInterface;

/**
 * A granter is a producer of individual grants
 */
interface GranterInterface
{
    /**
     * Create a new and updated grant
     */
    public function grant(GrantInterface $grant): GrantInterface;
}
