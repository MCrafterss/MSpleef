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

  class Main extends PluginBase implements Listener {

  	public $TaskID1;// i will use it
    public $ticks = 0;//Yes

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
  
  public function GameStart(){
    $players = $this->getServer()->getOnlinePlayers();
    $this->TaskID1 = $this->getServer()->getScheduler()->scheduleRepeatingTask(new GameStart($this, $players), 20)->getTaskId();
  }//GameSTart

  public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
    switch($cmd->getName()){
      case "ms":
      $this->GameStart();
    }//switch1

  }//onCommand
  }//Main
