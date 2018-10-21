<?php

namespace asylgrp\matchmaker\Filter;

/**
 * Wrapps the result of an applied filter
 */
interface ResultInterface
{
    /**
     * Check if filtering was successful
     */
    public function isSuccess(): bool;

    /**
     * Get message describing filtering
     */
    public function getMessage(): string;
}
