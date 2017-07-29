<?php

namespace RTG\JDT;

/**
 * Created by PhpStorm.
 * User: RTG
 * Date: 29/7/2017
 * Time: 7:13 PM
 */

class Task extends \pocketmine\scheduler\PluginTask {

    public $plugin;

    public function __construct(Loader $plugin)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {

        $jdc = new Query("jdcraft.net", 19132, $this->plugin, 'JDCraft');
        $jde = new Query("jdcraft.net", 19133, $this->plugin, 'JDEnterprise');
        $bc = new Query("jdcraft.net", 20000, $this->plugin, 'Blazecraft');

    }

}