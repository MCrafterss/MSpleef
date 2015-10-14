<?php

namespace TahaTheHacker\MSpleef;

use pocketmine\plugin\PluginBase;
use pocketmine\item\item;
use pocketmine\level\Position;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\scheduler\ServerScheduler;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\block\Block;

  class Main extends PluginBase implements Listener {

  	public $gameStartTask;// i will use it
    public $seconds = 0;//Yes

  public function onEnable(){
       if(!file_exists($this->getDataFolder() . "config.yml") || !file_exists($this->getDataFolder() . "Items.yml") || !file_exists($this->getDataFolder() . "rewards.yml")) {
      @mkdir($this->getDataFolder());
      file_put_contents($this->getDataFolder() . "config.yml",$this->getResource("config.yml"));
      file_put_contents($this->getDataFolder() . "Items.yml",$this->getResource("Items.yml"));
      file_put_contents($this->getDataFolder() . "rewards.yml",$this->getResource("rewards.yml"));
    }//!File_exists

      $this->Items = yaml_parse(file_get_contents($this->getDataFolder() . "Items.yml"));
      $this->reward = yaml_parse(file_get_contents($this->getDataFolder() . "rewards.yml"));
      $this->yml = yaml_parse(file_get_contents($this->getDataFolder() . "config.yml"));

   		$this->getServer()->getLogger()->debug("Config files have been saved!");

   		$this->getServer()->getPluginManager()->registerEvents($this, $this);

   		$this->getServer()->getLogger()->info("§l§6M§bSpleef §aEnabled§c!");
   		
  }//onEnable
  
  public function gameStart(){
    $this->gameStarted = true;
    $level = $this->getServer()->getLevelByName($this->yml["spleef-world"]);
    for($x = $this->yml["spleef-Min-floor-X"]; $x <= $this->yml["spleef-Max-floor-X"]; $x++):
    for($y = $this->yml["spleef-Min-floor-Y"]; $y <= $this->yml["spleef-Max-floor-Y"]; $y++):
    for($z = $this->yml["spleef-Min-floor-Z"]; $z <= $this->yml["spleef-Max-floor-X"]; $z++):
            $level->setBlock(new Vector3($x, $y, $z), Block::get($this->yml["spleef-floor-reset-block-ID"]));
          endfor;
          endfor;
          endfor;
          $this->gameStartTask = $this->getServer()->getScheduler()->scheduleRepeatingTask(new GameEnd($this), 20)->getTaskId();
          $this->getServer()->broadcastMessage("Spleef Game Started!");
  }//GameStart

  public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
    switch($cmd->getName()){
      case "ms":
      if (isset($args[0])){
    switch($args[0]){
      case "start":
      $this->gameStart();
      case "stop":
      if($this->gameStarted = true){
      $this->gameStarted = false;
    }//If
    }//switch 2
      }//isset
    }//switch1

  }//onCommand
  }//Main
