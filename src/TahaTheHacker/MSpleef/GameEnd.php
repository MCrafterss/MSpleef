<?php

namespace TahaTheHacker\MSpleef;

use pocketmine\scheduler\PluginTask;
use pocketmine\item\item;
use pocketmine\level\Position;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\item\ItemBlock;
use pocketmine\event\block\BlockEvent;
use pocketmine\network\protocol\UpdateBlockPacket;
use TahaTheHacker\MSpleef\Main;

class GameEnd extends PluginTask{

  public function __construct(Main $plugin){
    $this->plugin = $plugin;
    parent::__construct($plugin);
  }

  public function onRun($tick){
  $this->plugin->seconds++;

  $level = $this->plugin->getServer()->getLevelByName($this->plugin->yml["spleef-world"]);

      if($this->plugin->seconds === $this->plugin->yml["spleef-time"]){

    for($x = $this->plugin->yml["spleef-Min-floor-X"]; $x <= $this->plugin->yml["spleef-Max-floor-X"]; $x++){
    for($y = $this->plugin->yml["spleef-Min-floor-Y"]; $y <= $this->plugin->yml["spleef-Max-floor-Y"]; $y++){
    for($z = $this->plugin->yml["spleef-Min-floor-Z"]; $z <= $this->plugin->yml["spleef-Max-floor-X"]; $z++){
        $level->setBlock(new Vector3($x, $y, $z), Block::get(7,0));
    }
    }
    }
      $this->plugin->gameStarted = false;
      $this->plugin->seconds = 0;
      $this->plugin->getServer()->broadcastMessage("Spleef Game Ended!");
      $this->plugin->getServer()->getScheduler()->cancelTask($this->plugin->gameEndTask);

  }//If Seconds.
  }//onRun

}//Class
