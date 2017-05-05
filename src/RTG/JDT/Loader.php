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

class Loader extends PluginBase implements Listener{
    
    private $fetchedData;
    private $server;

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);        
        
    }
    
    public function sendChestInventory(Player $player){
        $nbt = new CompoundTag('', [
                new StringTag('id', Tile::CHEST),
                new IntTag('Transfer Hub', 1),
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
        $JDC = Item::get(279, 0, 1);
        $JDE = Item::get(279, 0, 1);
        $BC = Item::get(279, 0, 1);
        $JDC->setCustomName(TF::YELLOW . "JDCraft : 19132\n " . TF::RED . $this->getPl('jdcraft.net', 19132) . " players online!");
        $JDE->setCustomName(TF::GREEN . "JDEnterprise : 19133\n " . TF::RED . $this->getPl('jdcraft.net', 19133) . " players online!");
        $BC->setCustomName(TF::RED . "BlazeCraft : 20000\n " . TF::RED . $this->getPl('jdcraft.net', 20000) . " players online!");
        $tile->getInventory()->setItem(0, $JDC);
        $tile->getInventory()->setItem(2, $JDE);
        $tile->getInventory()->setItem(4, $BC);
        $player->addWindow($tile->getInventory());
    }
    
    public function query($host, $port) {
        
        $this->server = $this->UT3Query($host, $port);
        
        if ($this->server === null) {
            return true;
        }
        
        $this->fetchedData = [
            'server_on' => $this->server[15]
        ];
            
    }
        
    public function getPl($host, $port) {
        $this->query($host, $port);
        return $this->fetchedData['server_on'];
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
           if($newItem->getName() === TF::YELLOW . "JDCraft : 19132"){
               $event->setCancelled();
               $this->onTransfer($player, 'jdcraft.net', 19132);
                  return;
          } elseif ($newItem->getName() === TF::GREEN . "JDEnterprise : 19133") {
              $event->setCancelled();
              $this->onTransfer($player, 'jdcraft.net', 19133);
          } elseif ($newItem->getName() === TF::RED . "BlazeCraft : 20000") {
              $event->setCancelled();
              $this->onTransfer($player, 'jdcraft.net', 20000);
          }
          
    }
    
    private function UT3Query($host, $port) {
        
        $socket = @fsockopen("udp://" . $host, $port);
        if (!$socket)
            return null;
        $online = @fwrite($socket, "\xFE\xFD\x09\x10\x20\x30\x40\xFF\xFF\xFF\x01");
        if (!$online)
            return null;
        $challenge = @fread($socket, 1400);
        if (!$challenge)
            return null;
        $challenge = substr(preg_replace("/[^0-9-]/si", "", $challenge), 1);
        $query = sprintf("\xFE\xFD\x00\x10\x20\x30\x40%c%c%c%c\xFF\xFF\xFF\x01",
            $challenge >> 24, $challenge >> 16, $challenge >> 8, $challenge >> 0);
        if (!@fwrite($socket, $query))
            return null;
        $response = array();
        $response[] = @fread($socket, 2048);
        $response = implode($response);
        $response = substr($response, 16);
        $response = explode("\0", $response);
        array_pop($response);
        array_pop($response);
        return $response;
        
    }
    
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
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