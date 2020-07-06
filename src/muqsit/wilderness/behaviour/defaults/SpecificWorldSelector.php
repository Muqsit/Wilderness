<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour\defaults;

use InvalidArgumentException;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;

final class SpecificWorldSelector implements WorldSelector{

	public static function fromConfiguration(array $configuration) : self{
		return new self($configuration["world"]);
	}

	/** @var string */
	private $world;

	/** @var Server */
	private $world_manager;

	public function __construct(string $world){
		$this->world = $world;
		$this->world_manager = Server::getInstance();
	}

	public function select(Player $player) : Level{
		$world = $this->world_manager->getLevelByName($this->world);
		if($world === null){
			throw new InvalidArgumentException("Cannot teleport player to unloaded world {$this->world}");
		}
		return $world;
	}
}