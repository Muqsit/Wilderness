<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils;

use pocketmine\world\World;

final class Position2D{

	/** @var float */
	public $x;

	/** @var float */
	public $z;

	/** @var World */
	public $world;

	public function __construct(float $x, float $z, World $world){
		$this->x = $x;
		$this->z = $z;
		$this->world = $world;
	}
}