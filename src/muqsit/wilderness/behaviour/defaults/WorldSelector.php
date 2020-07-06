<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour\defaults;

use pocketmine\level\Level;
use pocketmine\Player;

interface WorldSelector{

	/**
	 * @param mixed[] $configuration
	 * @return static
	 */
	public static function fromConfiguration(array $configuration);

	public function select(Player $player) : Level;
}