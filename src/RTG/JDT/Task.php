<?php

namespace RTG\JDT;

use pocketmine\scheduler\PluginTask;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;

use RTG\JDT\Loader;
use RTG\JDT\Query;

class Task extends PluginTask {

	public $plugin;

	public function __construct(Loader $plugin) {
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun($currentTick) {

		// jdc
		$jdc = new Query('jdcraft.net', 19132);
		$res = $jdc->getPl('jdcraft.net', 19132);
		$file = new \SQLite3("storage.db");
		$sql = "INSERT INTO jdcraft WHERE count = '$res'";
		$file->query($sql);

		// bc
		$jdc = new Query('jdcraft.net', 20000);
		$res = $jdc->getPl('jdcraft.net', 20000);
		$file = new \SQlite3 ("storage.db");
		$sql = "INSERT INTO blazecraft WHERE count = '$res'";
		$file->query($sql);

	}


}


?>