<?php

namespace TahaTheHacker\MSpleef;

use pocketmine\scheduler\PluginTask;
use pocketmine\item\item;
use pocketmine\level\Position;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Level;
use pocketmine\math\Vector3;

class GameStart extends PluginTask{

  public function __construct(Main $plugin, $players){
    $this->plugin = $plugin;
    $this->players = $players
    parent::__construct($plugin);
  }

  public function onRun($tick){
    $this->plugin->ticks++;
    if($this->plugin->ticks === 1){
    	$level = $this->plugin->getServer()->getLevelByName($this->plugin->yml["spleef-world"]);
    for($x = $this->plugin->yml["spleef-Min-floor-X"]; $x <= $this->plugin->yml["spleef-Max-floor-X"]; $x++){ 
    for($y = $this->plugin->yml["spleef-Min-floor-Y"]; $y <= $this->plugin->yml["spleef-Max-floor-Y"]; $y++){
    for($z = $this->plugin->yml["spleef-Min-floor-Z"]; $z <= $this->plugin->yml["spleef-Max-floor-X"]; $z++){
            $level->setBlock(new Vector3($x, $y, $z), Block::get($this->plugin->yml["spleef-floor-reset-block-ID"]));
        }//for3
    }//for2
}//for1
    }//ticks
     if($this->plugin->ticks === 120){
    for($x = $this->plugin->yml["spleef-Min-floor-X"]; $x <= $this->plugin->yml["spleef-Max-floor-X"]; $x++){ 
    for($y = $this->plugin->yml["spleef-Min-floor-Y"]; $y <= $this->plugin->yml["spleef-Max-floor-Y"]; $y++){
    for($z = $this->plugin->yml["spleef-Min-floor-Z"]; $z <= $this->plugin->yml["spleef-Max-floor-X"]; $z++){
     	$level->setBlock(new Vector3($x, $y, $z), Block::get(7));
     	$this->plugin->getServer()->getScheduler()->cancelTask($this->plugin->TaskID1);
     }//FOR33
 }//FOR22
}//FOR11
     }//Ticks2
  }//onRun

}//Class
