<?php

namespace RTG\JDT;

/* Essentials */
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\network\protocol\TransferPacket;
use pocketmine\command\CommandExecutor;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase implements Listener {
    
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function onTransfer(Player $p, $ip, $port) {
        
        $pk = new \pocketmine\network\protocol\TransferPacket();
        $pk->address = $ip;
        $pk->port = (int) $port;
        $p->dataPacket($pk);
        $p->sendMessage("[Transfer] Executing...");
        $name = $p->getName();
        $this->getServer()->broadcastMessage("[Server] Transfering $name to the server the player requested using JDTransfer!");
        
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        
        switch(strtolower($command->getName())) {
            
            case "transfer":
                
                if(isset($args[0])) {
                    switch($args[0]) {
                        
                        case "help":
                            
                            $sender->sendMessage(" -- JD Transfer 1.0 Beta -- ");
                            $sender->sendMessage(" ");
                            $sender->sendMessage(TF::RED . "[Transfer] /transfer [ServerName]");
                            $sender->sendMessage(TF::RESET . " - Available Servers - ");
                            $sender->sendMessage(" ");
                            $sender->sendMessage(TF::RESET . " - JDC");
                            
                            return true;
                        break;
                        
                        case "JDC":
                            
                            $ip = "jdcraft.net";
                            $port = 19132;
                            $this->onTransfer($sender, $ip, $port);
                            
                            return true;
                        break;
                         
                    }
                       
                }
                else {
                    $sender->sendMessage("Usage: /transfer help");
                }
                
                return true;
            break;
   
        }
          
    }
    
    public function onDisable() {
        $this->getLogger()->warning("Turning off JDTransfer!");
    }
}