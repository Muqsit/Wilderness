<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils;

use pocketmine\world\World;

final class Position2D{

	public function __construct(
		public float $x,
		public float $z,
		public World $world
	){}
}