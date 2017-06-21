<?php

// some from AddWindow plugin!

namespace RTG\JDT;

use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tags\IntArrayTag;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;
use pocketmine\event\Cancellable;
use pocketmine\inventory\Inventory;
use pocketmine\event\entity\EntityInventoryChangeEvent;
use pocketmine\entity\Entity;
use pocketmine\network\protocol\TransferPacket;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\utils\TextFormat as TF;

use RTG\JDT\Task;

class Loader extends PluginBase implements Listener {

    public function onEnable() {

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Task($this), 10);
        
    }

    public function onCountGet($server) {

        $file = new SQlite3 ("database.db");
        $sql = "SELECT * FROM `$server`";
        $res = $file->query($sql);

        if ($row = $res->fetchArray(1)) {
            echo $row['count'];
        } else {
            return (string) "NULL";
        }

    }

}