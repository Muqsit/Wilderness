<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour\defaults;

use InvalidArgumentException;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\world\WorldManager;

final class SpecificWorldSelector implements WorldSelector{

	public static function fromConfiguration(array $configuration) : self{
		return new self($configuration["world"]);
	}

	private string $world;
	private WorldManager $world_manager;

	public function __construct(string $world){
		$this->world = $world;
		$this->world_manager = Server::getInstance()->getWorldManager();
	}

	public function select(Player $player) : World{
		$world = $this->world_manager->getWorldByName($this->world);
		if($world === null){
			throw new InvalidArgumentException("Cannot teleport player to unloaded world {$this->world}");
		}
		return $world;
	}
}