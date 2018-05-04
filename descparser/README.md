# descparser

A simple parser for verification descriptions.

```php
namespace asylgrp\descparser;

$parser = new DescParser;

$result = $parser->parse('This is a description @name #tag1 #tag2 rel:345 26/4-17');

assert($result->getNames() == ['name']);
assert($result->getTags() == ['tag1', 'tag2']);
assert($result->getRelations() == ['345']);
assert($result->getDate()->format('Y-m-d') == '2017-04-26');
```

Find dates based on current year

```php
namespace asylgrp\descparser;

$parser = new DescParser('2018');

$result = $parser->parse('26/4');

assert($result->getDate()->format('Y-m-d') == '2018-04-26');
```

If no date can be parsed null is returned. Many dates is an error..

```php
namespace asylgrp\descparser;

$parser = new DescParser;

$result = $parser->parse('No date is present here');

assert($result->getDate() === null);
```
