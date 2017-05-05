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

class Loader extends PluginBase implements Listener{

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function sendChestInventory(Player $player){
        $nbt = new CompoundTag('', [
                new StringTag('id', Tile::CHEST),
                new IntTag('Server Menu', 1),
                new IntTag('x', floor($player->x)),
                new IntTag('y', floor($player->y) - 4),
                new IntTag('z', floor($player->z))
        ]);
		
        $tile = Tile::createTile('Chest', $player->getLevel(), $nbt);
        $block = Block::get(Block::CHEST);
        $block->x = floor($tile->x);
        $block->y = floor($tile->y);
        $block->z = floor($tile->z);
        $block->level = $tile->getLevel();
        $block->level->sendBlocks([$player], [$block]);
        $test = Item::get(279, 0, 1);
        $test->setCustomName("JDCraft");
        $tile->getInventory()->setItem(0, $test);
        $player->addWindow($tile->getInventory());
    }

    public function onTransfer(Player $p, $ip, $port) {       
        $pk = new \pocketmine\network\protocol\TransferPacket();
        $pk->address = $ip;
        $pk->port = (int) $port;
        $p->dataPacket($pk);
    }

    public function onCheck(EntityInventoryChangeEvent $event){ // Item Use Checker!
        $player = $event->getEntity();
           $newItem = $event->getNewItem();
           if($newItem->getName() === "JDCraft"){
                $event->setCancelled();
                $this->onTransfer($player, 'jdcraft.net', 19132);
                  return;
          }
    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
            if($sender instanceof Player){
              switch(strtolower($cmd->getName())){
                case "jdt":
                      $sender->sendMessage("Added chest window!");
                      $this->sendChestInventory($sender);
                break;
              }
            }
    }
}