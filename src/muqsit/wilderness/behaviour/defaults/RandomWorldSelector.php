<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour\defaults;

use InvalidArgumentException;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;

final class RandomWorldSelector implements WorldSelector{

	public static function fromConfiguration(array $configuration) : self{
		return new self(...$configuration);
	}

	/** @var string[] */
	private $worlds;

	/** @var Server */
	private $world_manager;

	public function __construct(string ...$worlds){
		$this->worlds = $worlds;
		$this->world_manager = Server::getInstance();
	}

	public function select(Player $player) : Level{
		$world = $this->world_manager->getLevelByName($world_name = $this->worlds[array_rand($this->worlds)]);
		if($world === null){
			throw new InvalidArgumentException("Cannot teleport player to unloaded world {$world_name}");
		}
		return $world;
	}
}