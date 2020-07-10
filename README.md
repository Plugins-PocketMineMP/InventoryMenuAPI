# InventoryMenuAPI
A simple inventory menu api for PocketMine-MP

# How to use?

* Get normal chest
```php
$inv = \alvin0319\InventoryMenuAPI\InventoryMenuAPI::createChest("CHEST_NAME");
```

* Get Double chest
```php
$inv = alvin0319\InventoryMenuAPI\InventoryMenuAPI::createDoubleChest("CHEST_NAME");
```

* Handle Transaction
```php
/** @var \alvin0319\InventoryMenuAPI\inventory\InventoryBase $inv */
$inv->setTransactionHandler(function(\pocketmine\Player $player, \pocketmine\item\Item $input, \pocketmine\item\Item $output, int $slot, &$cancelled) : void{
    echo "Player {$player->getName()} put the item {$input} and took the item {$output} out of slot {$slot}.";
});
```
* What is `&$cancelled`?

`$cancelled` is used to cancel a transaction.

If `$cancelled = true;` the transaction is canceled, and if you do nothing or `$cancelled = false;` the transaction is not canceled.

* Handle opening inventory

```php
/** @var \alvin0319\InventoryMenuAPI\inventory\InventoryBase $inv */
$inv->setOpenHandler(function(\pocketmine\Player $player) : void{
    echo "{$player->getName()} has just opened inventory!";
});
```

* Handle closing inventory
```php
/** @var \alvin0319\InventoryMenuAPI\inventory\InventoryBase $inv */
$inv->setCloseHandler(function(\pocketmine\Player $player) : void{
    echo "{$player->getName()} has just closed inventory!";
});
```

* Send inventory
```php
/** @var \alvin0319\InventoryMenuAPI\inventory\InventoryBase $inv */
/** @var \pocketmine\Player $player */
$inv->send($player);
```