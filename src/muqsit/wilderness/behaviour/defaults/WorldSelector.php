<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour\defaults;

use pocketmine\player\Player;
use pocketmine\world\World;

interface WorldSelector{

	/**
	 * @param mixed[] $configuration
	 * @return static
	 */
	public static function fromConfiguration(array $configuration);

	public function select(Player $player) : World;
}