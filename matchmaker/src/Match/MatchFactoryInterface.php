<?php

namespace asylgrp\matchmaker\Match;

use asylgrp\matchmaker\Matchable\MatchableInterface;

interface MatchFactoryInterface
{
    public function createMatch(MatchableInterface ...$matchables): MatchInterface;
}
