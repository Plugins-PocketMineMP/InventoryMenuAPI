<?php
declare(strict_types=1);
namespace alvin0319\InventoryMenuAPI;

use alvin0319\InventoryMenuAPI\command\TestCommand;
use alvin0319\InventoryMenuAPI\inventory\ChestInventory;
use alvin0319\InventoryMenuAPI\inventory\DoubleChestInventory;
use alvin0319\InventoryMenuAPI\inventory\InventoryBase;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\plugin\PluginBase;

class InventoryMenuAPI extends PluginBase implements Listener{

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		if($this->getDescription()->getVersion() === "0.0.1"){
			$this->getServer()->getCommandMap()->register("inv", new TestCommand($this));
		}
	}

	public static function createDoubleChest(string $name) : DoubleChestInventory{
		return new DoubleChestInventory($name);
	}

	public static function createChest(string $name) : ChestInventory{
		return new ChestInventory($name);
	}

	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		$player = $event->getPlayer();

		if($packet instanceof ContainerClosePacket){
			$inv = $player->getWindow($packet->windowId);
			if($inv instanceof InventoryBase){
				$pk = new ContainerClosePacket();
				$pk->windowId = $player->getWindowId($inv);
				$player->sendDataPacket($pk);
				$event->setCancelled();
				$inv->onClose($player);
			}
		}
	}

	public function onInventoryTransaction(InventoryTransactionEvent $event) : void{
		$player = $event->getTransaction()->getSource();
		$actions = $event->getTransaction()->getActions();
		foreach($actions as $action){
			if($action instanceof SlotChangeAction){
				$inv = $action->getInventory();
				if($inv instanceof InventoryBase){
					$input = $action->getTargetItem();
					$output = $action->getSourceItem();
					$slot = $action->getSlot();

					$inv->handleTransaction($player, $input, $output, $slot, $cancelled);
					$event->setCancelled($cancelled ?? false);
				}
			}
		}
	}
}