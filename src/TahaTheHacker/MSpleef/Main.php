<?php

namespace TahaTheHacker\MSpleef;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\item\item;
use pocketmine\event\Event;
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
use pocketmine\item\ItemBlock;
use pocketmine\event\block\BlockEvent;
use pocketmine\network\protocol\UpdateBlockPacket;

  class Main extends PluginBase implements Listener {

    public $gameStartTask;// i will use it
    public $seconds = 0;//Yes
   //Config files
    public $items;
    public $reward;
    public $yml;
   //Game
    public $gameStarted = false;

  public function onEnable(){
     //Initializing config files
      $this->saveDefaultConfig();
      $this->saveResource("Items.yml");
      $this->saveResource("rewards.yml");
      $items = new Config($this->getDataFolder()."Items.yml",Config::YAML);
      $this->items = $items->getAll();
      $rewards = new Config($this->getDataFolder()."rewards.yml",Config::YAML);
      $this->rewards = $rewards->getAll();
      $yml = new Config($this->getDataFolder()."config.yml",Config::YAML);
      $this->yml = $yml->getAll();
	$this->getServer()->getLogger()->debug("Config files have been saved!");

   		$this->getServer()->getPluginManager()->registerEvents($this, $this);

   		$this->getServer()->getLogger()->info(TextFormat::BOLD.TextFormat::GOLD."M".TextFormat::AQUA."Spleef ".TextFormat::GREEN."Enabled".TextFormat::RED."!");
   		
  }//onEnable
  
  public function gameStart(){
    $this->gameStarted = true;
    $level = $this->getServer()->getLevelByName($this->yml["spleef-world"]);
    for($x = $this->yml["spleef-Min-floor-X"]; $x <= $this->yml["spleef-Max-floor-X"]; $x++){
    for($y = $this->yml["spleef-Min-floor-Y"]; $y <= $this->yml["spleef-Max-floor-Y"]; $y++){
    for($z = $this->yml["spleef-Min-floor-Z"]; $z <= $this->yml["spleef-Max-floor-X"]; $z++){
    	    $level->setBlock(new Vector3($x, $y, $z), Block::get(0));//prevents from a client-side issue when breaking the snow it becomes the last block it changed. (bedrock) so then players won't fall.
            $level->setBlock(new Vector3($x, $y, $z), Block::get($this->yml["spleef-floor-reset-block-ID"],$this->yml["spleef-floor-reset-block-damage"]));
          }
          }
          }
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
