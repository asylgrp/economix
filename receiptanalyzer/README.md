# receiptanalyzer

Analyze receipts with added receiver information.

## Quickstart

Checkout `DefaultAnalysis::analyze()` for some quick action.

<!-- @ignore -->
```php
namespace asylgrp\receiptanalyzer;

DefaultAnalysis::analyze((new CsvFactory)->fromFile('my-data.csv'));
```

## Usage

Load csv data using any of the `fromString()` or `fromFile()` methods of
`CsvFactory`. The following example data is used in all examples throughout
this file..

<!--
@example receipts
@exampleContext
-->
```php
$receipts = (new asylgrp\receiptanalyzer\CsvFactory)->fromString('
"kp", "jan",       "feb",     "mars"
"A",  "1;100;U,G", "2;200;G", "3;300;U"
"A",  "",          "",        "3;300;U,A"
"B",  "4;100;A",   "5;200;G", "6;300;G"
"C",  "7;100"
');
```

### Using custom cell formats

If your cell data is formatted deiiferently pass a cell parser callable to
`CsvFactory::__construct()` definied as:

<!-- @ignore -->
```php
function (string $cellData): ?Cell {
}
```

### Data access

#### Get receipts using `getReceipts()`

<!-- @example getReceipts -->
```php
assert(count($receipts->getReceipts()) == 8);
```

#### Get unique contacts using `getContacts()`

<!-- @example getContacts -->
```php
assert($receipts->getContacts() == ['kp']);
```

#### Get unique receivers using `getReceivers()`

<!-- @example getReceivers -->
```php
assert($receipts->getReceivers() == ['A', 'B', 'C']);
```

#### Get unique periods using `getPeriods()`

<!-- @example getPeriods -->
```php
assert($receipts->getPeriods() == ['jan', 'feb', 'mars']);
```

#### Get unique tags using `getTags()`

<!-- @example getTags -->
```php
assert($receipts->getTags() == ['U', 'G', 'A']);
```

#### Summarize amounts using `getTotalAmount()`

<!-- @example getTotalAmount -->
```php
assert($receipts->getTotalAmount()->getString() == '1600');
```

### Filtering

#### Filter by contact using  `whereContact()` and `whereNotContact()`

<!-- @example whereContact -->
```php
assert(count($receipts->whereContact('kp')) == 8);
```

#### Filter by receiver using  `whereReceiver()` and `whereNotReceiver()`

<!-- @example whereReceiver -->
```php
assert(count($receipts->whereReceiver('A')) == 4);
```

#### Filter by period using  `wherePeriod()` and `whereNotPeriod()`

<!-- @example wherePeriod -->
```php
assert(count($receipts->wherePeriod('mars')) == 3);
```

#### Filter by tag using  `whereTag()` and `whereNotTag()`

<!-- @example whereTag -->
```php
assert(count($receipts->whereTag('G')) == 4);
```

#### Filtering logic

All filtering methods take multiple arguments, representing the logical
disjunction. For example you may filter receipts tagged with either `A` or `U`
by issuing:

<!-- @example filterOr -->
```php
assert(count($receipts->whereTag('A', 'U')) == 4);
```

Conjunction is achieved by chaining filters. Filter receipts tagged with both
`A` and `U`:

<!-- @example filterAnd -->
```php
assert(count($receipts->whereTag('A')->whereTag('U')) == 1);
```

Notably for the `whereNotTag` filter this relation is reversed. So you
filter receipts not tagged with BOTH `A` and `U`:

<!-- @example filterNand -->
```php
assert(count($receipts->whereNotTag('A', 'U')) == 7);
```

Or you may filter receipts not tagged with neither `A` or `U`:

<!-- @example filterNor -->
```php
assert(count($receipts->whereNotTag('A')->whereNotTag('U')) == 4);
```

### Examples

Some cookbook examples. Nothing here is optimized for speed, it's all
expressiveness and developer centric..

#### Count and summarize receipts tagged with a specific tag

<!-- @example exampleCountTag -->
```php
$taggedReceipts = $receipts->whereTag('G');
assert(count($taggedReceipts) == 4);
assert($taggedReceipts->getTotalAmount()->getString() == '800');
```

#### Find receivers with only one registered receipt

<!-- @example exampleFindSingleReceivers -->
```php
$found = [];

foreach ($receipts->getReceivers() as $receiver) {
    if (count($receipts->whereReceiver($receiver)) == 1) {
        $found[] = $receiver;
    }
}

assert($found == ['C']);
```

#### Find duplicate receipts from the same receiver and period

<!-- @example exampleFindDuplicates -->
```php
$duplicates = $receipts->getDuplicateReceipts(function ($receipt) {
    return "{$receipt->getReceiverName()}:{$receipt->getPeriod()}";
});

assert(count($duplicates) == 1);
```
