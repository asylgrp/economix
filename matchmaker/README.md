# matchmaker

Match payouts with receipts.

The entry point for this package is the [`MatchMaker`](src/MatchMaker.php),
loaded with a set of [`matchers`](src/Matcher). Matchers are able to group 1 or
more [`matchables`](src/Matchable) together as [`matches`](src/Match). The
matchmaker creates a [`MatchCollection`](src/Match/MatchCollection.php).

## Matchability

This package ships with a simple [`Matchable`](src/Matchable/Matchable.php). You
may also supply your own [`MatchableInterface`](src/Matchable/MatchableInterface.php)
implementation.

> :information_source: Note that the order of matchables matters. Sort items
> oldest first if matching older payouts should be a priority.

## Balanceability

As definied in the [`MatchInterface`](src/Match/MatchInterface.php) matches
can be either successful (the amounts of all matched items balance out) or
failures (balance is non-zero). In some cases however you want a non-balanced
match to be reported as a success (a receipt of 99 kr matched to a payout of
100). Enter balanceability. A balanceable match is a match that can be amended
with one additional item to balance an acceptable diff. Balanceable matches are
thus always successful, as they are either balanced from the start, or can
programmatically be made to balance. Non-balanceable matches can, well, not be
balanced in this way, and are only reported as successful if balanced as is.

The inspector method `isBalanceable()` can be used to check if the item can be
balanced in it's current state (the method will only report true if item is
currently not balanced).

Generating balancing items is out of the scope of this package. But please see
the `getSuccessful()`, `getFailures()` and `getBalanceables()` methods of the
[`MatchCollection`](src/Match/MatchCollection.php) class.

<!--
@exampleContext
@ignore
```php
namespace asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\MatchMaker;
use asylgrp\matchmaker\Matchable\Matchable;
use byrokrat\amount\Amount;

function dump_matches($matchCollection)
{
    echo implode(
        ',',
        array_map(
            function ($match) {
                return implode(
                    '-',
                    array_map(
                        function ($matched) {
                            return $matched->getId();
                        },
                        $match->getMatched()
                    )
                );
            },
            $matchCollection->getMatches()
        )
    );
}
```
-->

## Matching individual matchables

The most basic matcher is the [`SingleMatcher`](src/Matcher/SingleMatcher.php)
which works by matching each matchable with nothing more then itself.

> :information_source: Terminate your list of matchers with a `SingleMatcher`
> to make sure non-matched items are reported as failures.

<!-- @example SingleMatcher -->
<!-- @expectOutput /^1,2$/ -->
```php
$matchMaker = new MatchMaker(new SingleMatcher);

$matches = $matchMaker->match(
    new Matchable('1', 'desc', new \DateTimeImmutable, new Amount('100')),
    new Matchable('2', 'desc', new \DateTimeImmutable, new Amount('-100'))
);

// 1,2
dump_matches($matches);
```

## Matching on date and amount

Use [`DateAndAmountMatcher`](src/Matcher/DateAndAmountMatcher.php) to match
pairs of matchables based on date and amount. You may specify the allowed
deviations in days and percentages.

> :information_source: By default `DateAndAmountMatcher` creates balanceable
> matches. Override by passing a `MatchFactoryInterface`
> implementation at construct. For more information see *Balanceability*.

<!-- @example DateAndAmountMatcher -->
<!-- @expectOutput /^1-2,3,4$/ -->
```php
$matchMaker = new MatchMaker(
    new DateAndAmountMatcher(new DateComparator(6), new AmountComparator(0.05)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', '', new \DateTimeImmutable('2018-04-30'), new Amount('-100')),
    new Matchable('2', '', new \DateTimeImmutable('2018-05-03'), new Amount('100')),
    new Matchable('3', '', new \DateTimeImmutable('2018-06-29'), new Amount('100')),
    new Matchable('4', '', new \DateTimeImmutable('2018-07-29'), new Amount('-100'))
);

// 1-2,3,4
dump_matches($matches);
```

## Matching on predefined relations

The [`RelatedMatcher`](src/Matcher/RelatedMatcher.php) matches items marked as
related. Note below how `1-2-3` are matched as the relation is made explicit in
`3`, but that `6` is not connected to `4` and `5`.

> :information_source: Start your list of matchers with a `RelatedMatcher`
> to make sure explicit relations are honored.

<!-- @example RelatedMatcher -->
<!-- @expectOutput /^3-1-2,4-5,6$/ -->
```php
$matchMaker = new MatchMaker(
    new RelatedMatcher,
    new DateAndAmountMatcher(new DateComparator(6), new AmountComparator(0.05)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', '', new \DateTimeImmutable('2018-04-30'), new Amount('100')),
    new Matchable('2', '', new \DateTimeImmutable('2018-05-03'), new Amount('-99')),
    new Matchable('3', '', new \DateTimeImmutable('2018-06-29'), new Amount('-1'), ['1', '2']),
    new Matchable('4', '', new \DateTimeImmutable('2018-04-30'), new Amount('100')),
    new Matchable('5', '', new \DateTimeImmutable('2018-05-03'), new Amount('-99')),
    new Matchable('6', '', new \DateTimeImmutable('2018-06-29'), new Amount('-1'))
);

// 3-1-2,4-5,6
dump_matches($matches);
```

## Matching zero amount items

In some configurations items with zero amount might be included in matches
where it is not desirable. Use the [`ZeroAmountMatcher`](src/Matcher/ZeroAmountMatcher.php)
to match these items as single matches.

> :information_source: It is a good idea to add a `ZeroAmountMatcher` directly
> after the `RelatedMatcher` when creating your matchmaker. That makes it
> possible to specify relations for deleted (zeroed) items and in this way
> report identified duplicates.

<!-- @example ZeroAmountMatcher -->
<!-- @expectOutput /^1$/ -->
```php
$matchMaker = new MatchMaker(new ZeroAmountMatcher);

$matches = $matchMaker->match(
    new Matchable('1', '', new \DateTimeImmutable, new Amount('0'))
);

// 1
dump_matches($matches);
```

## Matching groups

Match groups in one-to-many style using the [`GroupingMatcher`](src/Matcher/GroupingMatcher.php).

<!-- @example GroupingMatcher -->
<!-- @expectOutput /^1-4-3-2,5$/ -->
```php
$matchMaker = new MatchMaker(
    new GroupingMatcher(new DateComparator(12), new AmountComparator(0.05)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', '', new \DateTimeImmutable('2018-04-28'), new Amount('75')),
    new Matchable('2', '', new \DateTimeImmutable('2018-05-03'), new Amount('-25')),
    new Matchable('3', '', new \DateTimeImmutable('2018-05-03'), new Amount('-25')),
    new Matchable('4', '', new \DateTimeImmutable('2018-05-03'), new Amount('-25')),
    new Matchable('5', '', new \DateTimeImmutable('2018-05-03'), new Amount('-25'))
);

// 1-4-3-2,5
dump_matches($matches);
```

## Matching on amount alone

Use [`AmountMatcher`](src/Matcher/AmountMatcher.php) to match on amount alone.
It is equivalent to using a `DateAndAmountMatcher` with the date max deviation
set to `365`.

<!-- @example AmountMatcher -->
<!-- @expectOutput /^1-2$/ -->
```php
$matchMaker = new MatchMaker(
    new AmountMatcher(new AmountComparator(0.05)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', '', new \DateTimeImmutable('2018-05-30'), new Amount('100')),
    new Matchable('2', '', new \DateTimeImmutable('2018-06-15'), new Amount('-100'))
);

// 1-2
dump_matches($matches);
```

## Matching on date alone

Use [`DateMatcher`](src/Matcher/DateMatcher.php) to match on date alone. It is
equivalent to using a `DateAndAmountMatcher` with the amount max deviation set
to `1.0`.

> :information_source: Note that `DateMatcher` creates non-balanceable matches.
> A match on date alone is really uncertain, and should only be viewed as a
> partial match. For more information see *Balanceability*.

<!-- @example DateMatcher -->
<!-- @expectOutput /^1-2$/ -->
```php
$matchMaker = new MatchMaker(
    new DateMatcher(new DateComparator(6)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', '', new \DateTimeImmutable('2018-05-30'), new Amount('100')),
    new Matchable('2', '', new \DateTimeImmutable('2018-06-05'), new Amount('-50'))
);

// 1-2
dump_matches($matches);
```

## Putting it all together...

<!-- @example "fullstack" -->
<!-- @expectOutput /^2-1,3-4,5-6,7-9-8,10-11,12-13,14$/ -->
```php
$max10days = new DateComparator(10);
$max0percent = new AmountComparator(0.0);
$max5percent = new AmountComparator(0.05);

$matchMaker = new MatchMaker(
    new RelatedMatcher,
    new ZeroAmountMatcher,
    new DateAndAmountMatcher($max10days, $max0percent),
    new DateAndAmountMatcher($max10days, $max5percent),
    new GroupingMatcher($max10days, $max0percent),
    new GroupingMatcher($max10days, $max5percent),
    new AmountMatcher($max5percent),
    new DateMatcher($max10days),
    new SingleMatcher
);

$matches = $matchMaker->match(
    // matched by relation
    new Matchable('1', '', new \DateTimeImmutable('2018-05-30'), new Amount('100')),
    new Matchable('2', '', new \DateTimeImmutable('2018-06-05'), new Amount('-75'), ['1']),

    // matched by date and perfect amount
    new Matchable('3', '', new \DateTimeImmutable('2018-06-05'), new Amount('100')),
    new Matchable('4', '', new \DateTimeImmutable('2018-06-05'), new Amount('-100')),

    // matched by date and amount
    new Matchable('5', '', new \DateTimeImmutable('2018-06-05'), new Amount('100')),
    new Matchable('6', '', new \DateTimeImmutable('2018-06-05'), new Amount('-98')),

    //matched as group
    new Matchable('7', '', new \DateTimeImmutable('2018-06-05'), new Amount('100')),
    new Matchable('8', '', new \DateTimeImmutable('2018-06-05'), new Amount('-50')),
    new Matchable('9', '', new \DateTimeImmutable('2018-06-05'), new Amount('-49')),

    // matched even though date is off
    new Matchable('10', '', new \DateTimeImmutable('2018-06-05'), new Amount('100')),
    new Matchable('11', '', new \DateTimeImmutable('2018-08-05'), new Amount('-98')),

    // partial match
    new Matchable('12', '', new \DateTimeImmutable('2018-09-05'), new Amount('300')),
    new Matchable('13', '', new \DateTimeImmutable('2018-09-05'), new Amount('-200')),

    // no match
    new Matchable('14', '', new \DateTimeImmutable('2018-10-05'), new Amount('100'))
);

// 2-1,3-4,5-6,7-9-8,10-11,12-13,14
dump_matches($matches);
```
