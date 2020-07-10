<?php
declare(strict_types=1);
namespace alvin0319\InventoryMenuAPI\inventory;

use pocketmine\block\BlockIds;
use pocketmine\inventory\BaseInventory;
use pocketmine\math\Vector3;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Player;
use pocketmine\tile\Spawnable;

class ChestInventory extends InventoryBase{

	/** @var Vector3|null */
	protected $vector = null;

	protected $invName;

	public function __construct(string $invName){
		parent::__construct([], 27);
		$this->invName = $invName;
	}

	public function getName() : string{
		return "ChestInventory";
	}

	public function getDefaultSize() : int{
		return 27;
	}

	public function onOpen(Player $who) : void{
		BaseInventory::onOpen($who);
		$this->handleOpen($who);

		$this->vector = $who->add(0, 5)->floor();

		$x = $this->vector->x;
		$y = $this->vector->y;
		$z = $this->vector->z;

		$pk = new UpdateBlockPacket();
		$pk->x = $x;
		$pk->y = $y;
		$pk->z = $z;
		$pk->blockRuntimeId = RuntimeBlockMapping::toStaticRuntimeId(BlockIds::CHEST);
		$pk->flags = UpdateBlockPacket::FLAG_ALL_PRIORITY;
		$who->sendDataPacket($pk);

		$pk = new BlockActorDataPacket();
		$pk->x = $x;
		$pk->y = $y;
		$pk->z = $z;
		$pk->namedtag = (new NetworkLittleEndianNBTStream())->write(new CompoundTag("", [
			new StringTag("id", "Chest"),
			new IntTag("x", $x),
			new IntTag("y", $y),
			new IntTag("z", $z),
			new StringTag("CustomName", $this->invName)
		]));
		$who->sendDataPacket($pk);

		$pk = new ContainerOpenPacket();
		$pk->x = $x;
		$pk->y = $y;
		$pk->z = $z;
		$pk->windowId = $who->getWindowId($this);
		$who->sendDataPacket($pk);

		$this->sendContents($who);
	}

	public function onClose(Player $who) : void{
		BaseInventory::onClose($who);
		$this->handleClose($who);

		$x = $this->vector->x;
		$y = $this->vector->y;
		$z = $this->vector->z;

		$block = $who->getLevel()->getBlock($this->vector);

		$pk = new UpdateBlockPacket();
		$pk->x = $x;
		$pk->y = $y;
		$pk->z = $z;
		$pk->blockRuntimeId = RuntimeBlockMapping::toStaticRuntimeId($block->getId(), $block->getDamage()); // TODO: change this to $block->getRuntimeId();
		$pk->flags = UpdateBlockPacket::FLAG_ALL_PRIORITY;
		$who->sendDataPacket($pk);

		/** @var Spawnable $tile */
		if(($tile = $who->getLevel()->getBlock($this->vector)) instanceof Spawnable){
			$who->sendDataPacket($tile->createSpawnPacket());
		}else{
			$pk = new BlockActorDataPacket();
			$pk->x = $x;
			$pk->y = $y;
			$pk->z = $z;
			$pk->namedtag = (new NetworkLittleEndianNBTStream())->write(new CompoundTag());
			$who->sendDataPacket($pk);
		}

		/*
		$pk = new ContainerClosePacket();
		$pk->windowId = $who->getWindowId($this);
		$who->sendDataPacket($pk);
		*/
	}
}