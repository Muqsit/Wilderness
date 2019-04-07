<?php

declare(strict_types=1);
namespace muqsit\wilderness;

use muqsit\wilderness\math\Random2DCoordinateGenerator;
use muqsit\wilderness\types\ListInstance;
use muqsit\wilderness\types\Lists;
use muqsit\wilderness\utils\Language;
use muqsit\wilderness\utils\PlayerSession;
use muqsit\wilderness\utils\RegionUtils;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Wilderness extends PluginBase{

	public const LANGUAGE_FILE = "lang.yml";

	/** @var Random2DCoordinateGenerator */
	private $coordinate_generator;

	/** @var bool */
	private $chunk_load_flood_protection;

	/** @var bool */
	private $do_load_flood_protection;

	/** @var List */
	private $levels_list;

	public function onEnable() : void{
		$this->init();

		$coordinate_range = $this->getConfig()->get("coordinate-ranges");
		$this->coordinate_generator = new Random2DCoordinateGenerator(
			$coordinate_range["minx"], $coordinate_range["maxx"],
			$coordinate_range["minz"], $coordinate_range["maxz"]
		);

		$this->chunk_load_flood_protection = (bool) $this->getConfig()->get("chunk-load-flood-protection");
		$this->do_safe_spawn = (bool) $this->getConfig()->get("do-safe-spawn");

		if($this->chunk_load_flood_protection){
			$this->getServer()->getPluginManager()->registerEvents(new SessionHandler(), $this);
		}

		$this->language = new Language(yaml_parse_file($this->getDataFolder() . self::LANGUAGE_FILE));

		$levels = $this->getConfig()->get("levels");
		$this->levels_list = Lists::create($levels["type"], $levels["list"], "is_string");
	}

	private function init() : void{
		$this->saveDefaultConfig();
		$this->saveResource(self::LANGUAGE_FILE);
		Lists::init();
	}

	public function teleportPlayerToWilderness(Player $player, int $x, int $y, int $z, Level $level) : void{
		if($this->chunk_load_flood_protection){
			PlayerSession::get($player)->setCommandLock(false);
		}

		if($level->isClosed()){
			$player->sendMessage("Failed teleporting into the wilderness: the level was unloaded while trying to teleport you!");
			return;
		}

		$pos = new Vector3($x, $y, $z);
		if($player->teleport($this->do_safe_spawn ? $level->getSafeSpawn($pos) : Position::fromObject($pos, $level))){
			$player->sendMessage($this->language->translate("on-teleport", [
				"{PLAYER}" => $player->getName(),
				"{X}" => $x,
				"{Y}" => $y,
				"{Z}" => $z,
				"{LEVEL}" => $level->getFolderName()
			]));
		}
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
		if(!($sender instanceof Player)){
			$sender->sendMessage("This command can only be executed as a player.");
			return false;
		}

		$level = $sender->getLevel();
		$levelname = $level->getFolderName();
		if(!$this->levels_list->contains($levelname)){
			$sender->sendMessage($this->language->translate("on-command-failed-badlevel", [
				"{PLAYER}" => $sender->getName(),
				"{LEVEL}" => $levelname
			]));
			return false;
		}

		if($this->chunk_load_flood_protection){
			$session = PlayerSession::get($sender);
			if($session === null){
				// sender hasn't completely joined the server yet?
				return false;
			}

			if($session->hasCommandLock()){
				$sender->sendMessage($this->language->translate("on-command-failed-pending", [
					"{PLAYER}" => $sender->getName()
				]));
				return false;
			}

			$session->setCommandLock(true);
		}

		[$x, $z] = $this->coordinate_generator->generate();

		$chunkX = $x >> 4;
		$chunkZ = $z >> 4;

		$sender->sendMessage($this->language->translate("on-command", [
			"{PLAYER}" => $sender->getName(),
			"{X}" => $x,
			"{Z}" => $z,
			"{LEVEL}" => $levelname
		]));

		$cb = [$this, "teleportPlayerToWilderness"];
		RegionUtils::onChunkGenerate(
			$level, $chunkX, $chunkZ,
			function() use($sender, $x, $z, $level, $cb) : void{
				if($sender->isOnline()){
					$cb($sender, $x, $level->getHighestBlockAt($x, $z) + 1, $z, $level);
				}
			}
		);

		return true;
	}
}