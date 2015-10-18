<?php

namespace TahaTheHacker\MSpleef;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as Color;
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
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\item\ItemBlock;
use pocketmine\event\block\BlockEvent;
use pocketmine\network\protocol\UpdateBlockPacket;


  class Main extends PluginBase implements Listener {

   //Tasks
    public $gameEndTask;// i will use it
    public $seconds = 0;//Yes
    
   //Config files
    public $items;
    public $reward;
    public $yml;
    public $level;
   //Game
    public $gameStarted = false;

  public function onEnable(){
     $level = $this->getServer()->getLevelByName($this->yml["spleef-world"]);
     //Initializing config files
     
      $this->saveResource("config.yml");
      $this->saveResource("Items.yml");
      $this->saveResource("rewards.yml");
      
      $items = new Config($this->getDataFolder() . "Items.yml", Config::YAML);
      $this->items = $items->getAll();
      $rewards = new Config($this->getDataFolder() . "rewards.yml", Config::YAML);
      $this->rewards = $rewards->getAll();
      $yml = new Config($this->getDataFolder() . "config.yml", Config::YAML);
      $this->yml = $yml->getAll();
      
  $this->getLogger()->debug("Config files have been saved!");

      $this->getServer()->getPluginManager()->registerEvents($this, $this);
    if(!$this->getServer()->isLevelGenerated($level)){
      $this->getLogger()->error("The level you used on the config doesn't exist! stopping plugin or crash..");
      $this->getServer()->getPluginManager()->disablePlugin($this->getServer()->getPluginManager()->getPlugin("MSpleef"));
    }
    
    if(!$this->getServer()->isLevelLoaded($level)){
      $this->getServer()->loadLevel($level);
    }
      $this->getServer()->getLogger()->info(Color::BOLD . Color::GOLD . "M" . Color::AQUA . "Spleef " . Color::GREEN . "Enabled" . Color::RED . "!");
      
  }//onEnable
  
  public function gameStart(){
        $level = $this->getServer()->getLevelByName($this->yml["spleef-world"]);
      for($x = $this->yml["spleef-Min-floor-X"]; $x <= $this->yml["spleef-Max-floor-X"]; $x++){
      for($y = $this->yml["spleef-Min-floor-Y"]; $y <= $this->yml["spleef-Max-floor-Y"]; $y++){
      for($z = $this->yml["spleef-Min-floor-Z"]; $z <= $this->yml["spleef-Max-floor-X"]; $z++){
    $level->setBlock(new Vector3($x, $y, $z), Block::get(0));//prevents from a client-side issue when breaking the snow it becomes the last block it changed. (bedrock) so then players won't fall.

    $this->level = $this->getServer()->getLevelByName($this->yml["spleef-world"]);
    

    
            $level->setBlock(new Vector3($x, $y, $z), Block::get($this->yml["spleef-floor-reset-block-ID"],$this->yml["spleef-floor-reset-block-damage"]));
          }
          }
          }
          $this->gameEndTask = $this->getServer()->getScheduler()->scheduleRepeatingTask(new GameEnd($this), 20)->getTaskId();
          
          foreach($this->yml["spleef-start-messages"] as $msg){
            
          $this->getServer()->broadcastMessage($msg);
          
          }
        $this->gameStarted = true;
  }//GameStart

  public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
    switch($cmd->getName()){
      case "ms":
      if (isset($args[0])){
    switch(strtolower($args[0])){

      case "start":
      $this->gameStart();
      return true;
      break;

      case "stop":
      if($this->gameStarted == true){
        for($x = $this->yml["spleef-Min-floor-X"]; $x <= $this->yml["spleef-Max-floor-X"]; $x++){
        for($y = $this->yml["spleef-Min-floor-Y"]; $y <= $this->yml["spleef-Max-floor-Y"]; $y++){
        for($z = $this->yml["spleef-Min-floor-Z"]; $z <= $this->yml["spleef-Max-floor-X"]; $z++){
          $this->level->setBlock(new Vector3($x, $y, $z), Block::get(7));
          $this->getServer()->broadcastMessage("Spleef Game Ended!");
        }
        }
        }
      $this->gameStarted = false;
    }//If
    return true;
    break;
      case "reload":
      $plugin = $this->getServer()->getPluginManager()->getPlugin("MSpleef");
      $this->getServer()->getPluginManager()->disablePlugin($plugin);
      $this->getServer()->getPluginManager()->enablePlugin($plugin);
      return true;
      break;
    
    }//switch 2
      } else { $sender->sendMessage("Usage: /ms <start/stop>"); }//isset
    }//switch1

  }//onCommand
  
 public function gameStartButton(PlayerInteractEvent $event){
    if($event->getBlock()->getX() === $this->yml["spleef-start-block-X"] && $event->getBlock()->getY() === $this->yml["spleef-start-block-Y"] && $event->getBlock()->getZ() === $this->yml["spleef-start-block-Z"]){
      if($this->gameStarted === false){
        $this->gameStart();
      } else { $event->getPlayer()->sendMessage("Spleef game already started!"); }
    }//if1
 }//gameStartButton

 public function spleefItems(PlayerMoveEvent $event){
  $player = $event->getPlayer();
    foreach($this->items["Items"] as $i){
      for($x = $this->yml["spleef-Min-floor-X"]; $x <= $this->yml["spleef-Max-floor-X"]; $x++){
      for($y = $this->yml["spleef-Min-floor-Y"]; $y <= $this->yml["spleef-Max-floor-Y"]; $y++){
      for($z = $this->yml["spleef-Min-floor-Z"]; $z <= $this->yml["spleef-Max-floor-X"]; $z++){
   if(round($event->getFrom()->getX()) == round($x) && round($event->getFrom()->getY())-1 == round($y) && round($event->getFrom()->getZ()) == round($z)){
    $player->getInventory()->addItem(Item::get($i["id"], $i["damage"]));
    } elseif(round($event->getFrom()->getX()) == $x && round($event->getFrom()->getY())-2 == $y && round($event->getFrom()->getZ()) == $z){ $player->getInventory()->addItem(Item::get($i["id"], $i["damage"])); } else { $player->getInventory()->removeItem(Item::get($i["id"], $i["damage"])); }//if
  }//3
  }//2
  }//1
  }//foreach
 }//spleefItems
  }//Main
