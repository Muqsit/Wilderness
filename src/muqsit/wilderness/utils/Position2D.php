<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils;

use pocketmine\level\Level;

final class Position2D{

	/** @var float */
	public $x;

	/** @var float */
	public $z;

	/** @var Level */
	public $world;

	public function __construct(float $x, float $z, Level $world){
		$this->x = $x;
		$this->z = $z;
		$this->world = $world;
	}
}