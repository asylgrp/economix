# accounting2matchable

Transform accounting data to matchable items.

Internaly depends on `descparser` and `matchmaker`.

Load accounting data from SIE format.

<!-- @ignore -->
```php
$sieParser = (new \byrokrat\accounting\Sie4\Parser\ParserFactory)->createParser();
$accounting = $sieParser->parse(file_get_contents('verifications.se'));
```

<!--
@exampleContext
@ignore
```php
$sieParser = (new \byrokrat\accounting\Sie4\Parser\ParserFactory)->createParser();
$accounting = $sieParser->parse("
    #FLAGGA 1
    #KONTO 1920 Bank
    #KONTO 1501 Contact
    #KONTO 4000 Kvitterat
    #VER \"\" 1 20180830 \"description\"
    {
        #TRANS  1920 {} -200.00
        #TRANS  1501 {} 200.00
    }
    #VER \"\" 2 20180830 \"description\"
    {
        #TRANS  4000 {} 100.00
        #TRANS  1501 {} -100.00
    }
    #VER \"\" 3 20180830 \"description\"
    {
        #TRANS  4000 {} 100.00
        #TRANS  1501 {} -100.00
    }
");
```
-->

Pass current year (year of accounting) when creating your factory, to enable
the correct dates to be generated.

Generate matchables for all transactions to a specified account (including the
incoming balance) using the `createMatchablesForAccount` method.

<!-- @example accounting2matchable -->
```php
$factory = asylgrp\accounting2matchable\MatchableFactory::createFactoryForYear(2017);

$matchables = $factory->createMatchablesForAccount(
    $accounting->select()->getAccount('1501'),
    $accounting
);
```
