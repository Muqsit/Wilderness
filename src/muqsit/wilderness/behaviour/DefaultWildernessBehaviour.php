<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour;

use muqsit\wilderness\Loader;
use muqsit\wilderness\math\Random2DCoordinateGenerator;
use muqsit\wilderness\utils\Language;
use muqsit\wilderness\utils\lists\ListInstance;
use muqsit\wilderness\utils\Position2D;
use pocketmine\level\Position;
use pocketmine\Player;

class DefaultWildernessBehaviour extends ConfigurableBehaviour{

	/** @var Random2DCoordinateGenerator */
	protected $coordinate_generator;

	/** @var ListInstance<string> */
	protected $worlds_list;

	/** @var Language */
	protected $language;

	public function init(Loader $loader) : void{
		parent::init($loader);
		$config = $this->getConfig();

		$coordinate_range = $config->get("coordinate-ranges");
		$this->coordinate_generator = new Random2DCoordinateGenerator(
			$coordinate_range["minx"], $coordinate_range["maxx"],
			$coordinate_range["minz"], $coordinate_range["maxz"]
		);
		$this->worlds_list = BehaviourHelper::parseGrayList($config->get("worlds"));
		$this->language = BehaviourHelper::parseLanguage($config->get("language"));
	}

	public function generatePosition(Player $player) : ?Position2D{
		$world = $player->getLevelNonNull();
		if(!$this->worlds_list->contains($world->getFolderName())){
			$this->language->translateAndSend($player, "on-command-failed-badworld", [
				"{PLAYER}" => $player->getName(),
				"{WORLD}" => $world->getFolderName()
			]);
			return null;
		}

		[$x, $z] = $this->coordinate_generator->generate();
		$this->language->translateAndSend($player, "on-command", [
			"{PLAYER}" => $player->getName(),
			"{X}" => (string) $x,
			"{Z}" => (string) $z,
			"{WORLD}" => $world->getFolderName()
		]);
		return new Position2D($x + 0.5, $z + 0.5, $world);
	}

	public function onTeleportFailed(Player $player, int $reason) : void{
		if($reason === BehaviourTeleportFailReason::COMMAND_LOCK){
			$this->language->translateAndSend($player, "on-command-failed-pending", []);
		}
	}

	public function onTeleportSuccess(Player $player, Position $position) : void{
		$this->language->translateAndSend($player, "on-teleport", [
			"{PLAYER}" => $player->getName(),
			"{X}" => (string) $position->getFloorX(),
			"{Y}" => (string) $position->getFloorY(),
			"{Z}" => (string) $position->getFloorZ(),
			"{WORLD}" => $position->getLevelNonNull()->getFolderName()
		]);
	}
}