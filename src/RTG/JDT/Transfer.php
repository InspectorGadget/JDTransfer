<?php

namespace RTG\JDT;

use RTG\JDT\Loader;

/* Essentials */
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class Transfer implements CommandExecutor {
    
    public $plugin;
    
    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
    }
    
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $param) {
        switch(strtolower($cmd->getName())) {
                
                case "jdt":
                    
                    $sender->sendMessage("[Transfer] /jdt [ServerName]");
                    $sender->sendMessage(" ");
                    $sender->sendMessage("[Transfer] Server List: Revolution," . TF::GREEN . "JDE, JDB, JDMulti, BC, JDC, JDU, IB, JDCustom");
                    $sender->sendMessage("[Transfer]" . TF::RED . "If i miss any names out, please let me know - IG");
                    
                    
                        if(isset($param[0])) {
                            switch(strtolower($param[0])) {
                                
                                case "JDC":
                                    
                                    $ip = "jdcraft.net";
                                    $port = 19132;
                                    
                                    if($sender instanceof Player) {
                                        $this->plugin->onTransfer($sender, $ip, $port);
                                    }
                                    else {
                                        $sender->sendMessage("[Error] In-game!");
                                    }
                                    
                                    return true;
                                break;
                                 
                            }
                               
                        }
                        else {
                            $sender->sendMessage("Usage: /jdt");
                        }
                        
                        
                    return true;
                break;
              
        }
           
    }

}