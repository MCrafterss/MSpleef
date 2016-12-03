<?php

namespace MSpleef;

//Plugin - Utils
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as Color;
use pocketmine\utils\Config;

//Level - Position
use pocketmine\level\Position;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\level\Level;

//Command
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\command\ConsoleCommandSender;

//Server
use pocketmine\Server;
use pocketmine\scheduler\ServerScheduler;

//Network
use pocketmine\network\protocol\UpdateBlockPacket;

//Block - Item
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;

//Player - Events
use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\block\BlockEvent;

class MSpleef extends PluginBase implements Listener {
	
	public $prefix = Color::DARK_GRAY."[".Color::GOLD."M".Color::AQUA."Spleef".Color::DARK_GRAY."]".Color::WHITE." ";
	public $yml;
	public $level;
	public $gameStarted = false
	
	const GAME_STARTED = 'Started';//To-Do
	
	public function onEnable(){
		$config = $this->getConfig();
		//Initializing config files
		$this->saveResource("config.yml");
		//To-Do $this->saveResource("Items.yml");
		//To-Do $this->saveResource("rewards.yml");
		//To-Do $items = new Config($this->getDataFolder() . "Items.yml", Config::YAML);
		//To-Do $this->items = $items->getAll();
		//To-Do $rewards = new Config($this->getDataFolder() . "rewards.yml", Config::YAML);
		//To-Do $this->rewards = $rewards->getAll();
		$yml = new Config($this->getDataFolder()."config.yml", Config::YAML);
		$this->yml = $yml->getAll();
		$this->getLogger()->debug("Config files have been saved!");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$level = $this->yml["spleef-world"];
		if(!$this->getServer()->isLevelGenerated($level)){
			$this->getLogger()->error("The level you used in the config ( ".$level." ) doesn't exist! stopping plugin..");
			$this->getServer()->getPluginManager()->disablePlugin($this->getServer()->getPluginManager()->getPlugin("MSpleef"));
		}
		if(!$this->getServer()->isLevelLoaded($level)){
			$this->getServer()->loadLevel($level);
		}
		$this->getServer()->getLogger()->info(Color::BOLD.Color::GOLD."M".Color::AQUA."Spleef ".Color::GREEN."Enabled".Color::GRAY."!");  
	}
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
		foreach($this->yml["spleef-start-messages"] as $msg){
			$this->getServer()->broadcastMessage($msg);
		}
		$this->gameStarted = true;
		sleep($this->yml["spleef-time"]);
		for($x = $this->yml["spleef-Min-floor-X"]; $x <= $this->yml["spleef-Max-floor-X"]; $x++){
			for($y = $this->yml["spleef-Min-floor-Y"]; $y <= $this->yml["spleef-Max-floor-Y"]; $y++){
				for($z = $this->yml["spleef-Min-floor-Z"]; $z <= $this->yml["spleef-Max-floor-X"]; $z++){
					$level->setBlock(new Vector3($x, $y, $z), Block::get(7,0));
				}
			}
		}
		$this->gameStarted = false;
		foreach($this->yml["spleef-end-messages"] as $msg){
			$this->getServer()->broadcastMessage($msg);
		}
	}
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		switch($cmd->getName()){
			case "spleef":
			if($sender->hasPermission("ms.command")){
				if(isset($args[0])){
					switch(strtolower($args[0])){
						case "start":
						if($sender->hasPermission("ms.command.start")){
							$this->gameStart();
							return true;
							break;
						}
						case "stop":
						if($sender->hasPermission("ms.command.stop")){
							if($this->gameStarted == true){
								for($x = $this->yml["spleef-Min-floor-X"]; $x <= $this->yml["spleef-Max-floor-X"]; $x++){
									for($y = $this->yml["spleef-Min-floor-Y"]; $y <= $this->yml["spleef-Max-floor-Y"]; $y++){
										for($z = $this->yml["spleef-Min-floor-Z"]; $z <= $this->yml["spleef-Max-floor-X"]; $z++){
											$this->level->setBlock(new Vector3($x, $y, $z), Block::get(7));
											foreach($this->yml["spleef-end-messages"] as $msg){
												$this->getServer()->broadcastMessage($Msg);
											}
										}
									}
								}
								$this->gameStarted = false;
							}
							return true;
							break;
						}
						case "reload":
						if($sender->hasPermission("ms.command.reload")){
							$plugin = $this->getServer()->getPluginManager()->getPlugin("MSpleef");
							$this->getServer()->getPluginManager()->disablePlugin($plugin);
							$this->getServer()->getPluginManager()->enablePlugin($plugin);
							$sender->sendMessage(Color::GOLD."Plugin".Color::BOLD.Color::GREEN." succesfully ".Color::RESET.Color::RED."reloaded".Color::BLUE."!");
							return true;
							break;
						}
					}
				}
			}
		}
	}
	public function gameStartButton(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if($block->getX() === $this->yml["spleef-start-block-X"] && $block->getY() === $this->yml["spleef-start-block-Y"] && $block->getZ() === $this->yml["spleef-start-block-Z"]){
			if($this->gameStarted === false){
				$this->gameStart();
			}else{
				$player->sendMessage($this->yml["spleef-already-started-message"]); 
			}
		}
	}
}//End of File
