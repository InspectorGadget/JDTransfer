<?php

namespace RTG\JDT;

/* Essentials */
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\network\protocol\TransferPacket;
use pocketmine\command\CommandExecutor;

class Loader extends PluginBase {
    
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getCommand("transfer")->setExecutor(new TransferCommand ($this));
    }
    
    public function onTransfer($p, $ip, $port) {
        
        $pk = new \pocketmine\network\protocol\TransferPacket();
        $pk->address = $ip;
        $pk->port = (int) $port;
        $p->dataPacket($pk);
        $p->sendMessage("[Transfer] Executing...");
        
    }
    
    public function onDisable() {
        $this->getLogger()->warning("Turning off JDTransfer!");
    }
    
}