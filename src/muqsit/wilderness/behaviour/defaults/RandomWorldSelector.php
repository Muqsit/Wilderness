<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour\defaults;

use InvalidArgumentException;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\world\WorldManager;

final class RandomWorldSelector implements WorldSelector{

	public static function fromConfiguration(array $configuration) : self{
		return new self(...$configuration);
	}

	/** @var string[] */
	private array $worlds;

	private WorldManager $world_manager;

	public function __construct(string ...$worlds){
		$this->worlds = $worlds;
		$this->world_manager = Server::getInstance()->getWorldManager();
	}

	public function select(Player $player) : World{
		$world = $this->world_manager->getWorldByName($world_name = $this->worlds[array_rand($this->worlds)]);
		if($world === null){
			throw new InvalidArgumentException("Cannot teleport player to unloaded world {$world_name}");
		}
		return $world;
	}
}