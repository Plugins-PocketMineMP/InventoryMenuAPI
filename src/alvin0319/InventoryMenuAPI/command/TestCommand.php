<?php
declare(strict_types=1);
namespace alvin0319\InventoryMenuAPI\command;

use alvin0319\InventoryMenuAPI\InventoryMenuAPI;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\item\Item;
use pocketmine\Player;

class TestCommand extends PluginCommand{

	public function __construct(InventoryMenuAPI $plugin){
		parent::__construct("testinv", $plugin);
		$this->setPermission("op");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if($sender instanceof Player){
			switch($args[0] ?? "x"){
				case "chest":
				case "single":
					$inv = InventoryMenuAPI::createChest("Test");
					$inv->setOpenHandler(function(Player $player) : void{
						$this->getPlugin()->getLogger()->debug("Player {$player->getName()} has opened inventory.");
					});
					$inv->setCloseHandler(function(Player $player) : void{
						$this->getPlugin()->getLogger()->debug("Player {$player->getName()} has closed inventory.");
					});
					$inv->setTransactionHandler(function(Player $player, Item $input, Item $output, int $slot, &$cancelled = false) : void{
						$this->getPlugin()->getLogger()->debug("Received Input $input, Output $output, slot $slot from {$player->getName()}");
					});
					$inv->send($sender);
					break;
				case "double":
				case "doublechest":
					$inv = InventoryMenuAPI::createDoubleChest("Test2");
					$inv->setOpenHandler(function(Player $player) : void{
						$this->getPlugin()->getLogger()->debug("Player {$player->getName()} has opened inventory.");
					});
					$inv->setCloseHandler(function(Player $player) : void{
						$this->getPlugin()->getLogger()->debug("Player {$player->getName()} has closed inventory.");
					});
					$inv->setTransactionHandler(function(Player $player, Item $input, Item $output, int $slot, &$cancelled = false) : void{
						$this->getPlugin()->getLogger()->debug("Received Input $input, Output $output, slot $slot from {$player->getName()}");
						$cancelled = true;
					});
					$inv->send($sender);
					break;
			}
		}
		return true;
	}
}