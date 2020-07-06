<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour\defaults;

use pocketmine\level\Level;
use pocketmine\Player;

final class SelfWorldSelector implements WorldSelector{

	public static function fromConfiguration(array $configuration) : self{
		return new self();
	}

	public function select(Player $player) : Level{
		return $player->getLevelNonNull();
	}
}