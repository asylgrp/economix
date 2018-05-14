# decisionmaker

Create and manage payout decisions.

## TODO

Detta paket är inte färdigt..

### Allocation

```php
namespace Allocator;

// Allokera samma summa till varje requets (baserat på tillgängliga medel och optional max value..)
// TODO Detta är färdigskriver

$alloc = new LazyAllocator(new FixedGranterFactory);
$payouts = $alloc->allocate($money, $payouts);

// Men hur gör en då för att använda pre-set guarantee osv..
// TODO Detta är färdigskriver

$alloc = new StaticAllocator(new FixedGranter(new SEK('1000')));

// Det krävs någon form av builder så att detta blir enkelt att styra från ini..
// TODO AllocatorBuilder är inte skriven..

$allocator = (new AllocatorBuilder)
    ->addLazyFixed($max = new SEK('1000'))
    ->addLazyRatio()
    ->addStaticFixed($fixed)
    ->addStaticRatio($ratio)
    ->getAllocator();
```

### DecisionMaker

En DecisionMaker som innehåller denna logic...

```php
$alloc = (new AllocatorBuilder)->...->getAllocator();

$funds = new SEK('4500');

// TODO funkar det att göra id så? Kan ta hänsyn till claim dates osv...
// TODO finns decisionmaker\Utils\SyStemClock att använda..

return new Decision(
    $payoutRequestHasher->getHash($payoutRequests),
    $clock->now(),
    $funds,
    $alloc->allocate($funds, $payoutRequests)
);
```

### Arrayizer

* And then a `GrantArrayizer` producing something like this from Grants:

```php
[
    "desc" => "claim desc",
    "claimedAmount" => "100",
    "grantItems" => [
        ["50", "grant desc"],
        ["40", "other desc.."]
    ]
]
```

* Also a `PayoutRequestArrayizer` for jsonized "databases".
* And a `DecisionArrayizer` for serializing decisions...

### Presentation

PDF-genererar logiken med sortering och gruppering(?)...

* Lägg till antal KP:s till sidhuvud i beslut.
