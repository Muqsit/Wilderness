<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour\defaults;

use pocketmine\player\Player;
use pocketmine\world\World;

final class SelfWorldSelector implements WorldSelector{

	public static function fromConfiguration(array $configuration) : self{
		return new self();
	}

	public function select(Player $player) : World{
		return $player->getWorld();
	}
}