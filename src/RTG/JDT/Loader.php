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

class Loader extends PluginBase implements Listener {

    public function onEnable() {

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Task($this), 60);

        if (!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }

        $file = new \SQLite3($this->getDataFolder() . "sqlite.db");
        $sql = "CREATE TABLE `servers` (
                `id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                `name`	TEXT NOT NULL,
                `count`	INTEGER NOT NULL
                )";

        $file->query($sql);

    }

    public function sendChestInventory(Player $player){
        $nbt = new CompoundTag('', [
            new StringTag('id', Tile::CHEST),
            new IntTag('Transfer Hub', 1),
            new IntTag('x', $player->x),
            new IntTag('y', $player->y - 4),
            new IntTag('z', $player->z)
        ]);

        $tile = Tile::createTile('Chest', $player->getLevel(), $nbt);
        $block = Block::get(Block::CHEST);
        $block->x = $tile->x;
        $block->y = $tile->y;
        $block->z = $tile->z;
        $block->level = $tile->getLevel();
        $block->level->sendBlocks([$player], [$block]);
        $JDC = Item::get(279, 0, 1);
        $JDC->setCustomName(TF::YELLOW . "JDCraft : 19132\n " . TF::RED . $this->getSQL('JDCraft') . " players online!");
        $JDE = Item::get(279, 0, 1);
        $JDE->setCustomName(TF::YELLOW . "JDEnterprise : 19133\n " . TF::RED . $this->getSQL('JDEnterprise') . " players online!");
        $BC = Item::get(279, 0, 1);
        $BC->setCustomName(TF::YELLOW . "BlazeCraft : 20000\n " . TF::RED . $this->getSQL('Blazecraft') . " players online!");
        $tile->getInventory()->setItem(0, $JDC);
        $tile->getInventory()->setItem(2, $JDE);
        $tile->getInventory()->setItem(4, $BC);
        $player->addWindow($tile->getInventory());
    }

    public function getSQL($name) {

        $file = new \SQLite3($this->getDataFolder() . "sqlite.db");
        $sql = "SELECT * FROM servers WHERE name = '$name'";
        $res = $file->query($sql);

        while ($row = $res->fetchArray(1)) {
            return $row['count'];
        }

    }

    public function onTransfer(Player $p, $ip, $port) {
        $pk = new \pocketmine\network\mcpe\protocol\TransferPacket();
        $pk->address = $ip;
        $pk->port = (int) $port;
        $p->dataPacket($pk);
    }

    public function onCheck(EntityInventoryChangeEvent $event){ //
        $player = $event->getEntity();
        $newItem = $event->getNewItem();
        if($newItem->getName() === TF::YELLOW . "JDCraft : 19132\n " . TF::RED . $this->getSQL('JDCraft') . " players online!"){
            $event->setCancelled();
            $this->onTransfer($player, 'jdcraft.net', 19132);
            return;
        } elseif ($newItem->getName() === TF::YELLOW . "JDEnterprise : 19133\n " . TF::RED . $this->getSQL('JDEnterprise') . " players online!") {
            $event->setCancelled();
            $this->onTransfer($player, 'jdcraft.net', 19133);
        } elseif ($newItem->getName() === TF::YELLOW . "BlazeCraft : 20000\n " . TF::RED . $this->getSQL('BlazeCraft') . " players online!") {
            $event->setCancelled();
            $this->onTransfer($player, 'jdcraft.net', 20000);
        }

    }

    /**
     * @param CommandSender $sender
     * @param Command $cmd
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
        if($sender instanceof Player){
            switch(strtolower($cmd->getName())){
                case "jdt":
                    $sender->sendMessage("JDTransfer running...");
                    $this->sendChestInventory($sender);
                    break;
            }
        }
    }
}