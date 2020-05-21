<?php

declare(strict_types=1);
namespace muqsit\wilderness\math;

class Random2DCoordinateGenerator{

	/** @var ClosedInterval */
	private $x_interval;

	/** @var ClosedInterval */
	private $y_interval;

	public function __construct(int $minx, int $maxx, int $minz, int $maxz, int $centerx, int $centerz, int $blockedradius){
		$this->x_interval = new ClosedInterval($minx, $maxx, $centerx, $blockedradius);
		$this->y_interval = new ClosedInterval($minz, $maxz, $centerz, $blockedradius);
	}

	public function generate() : array{
		return [
			$this->x_interval->getRandom(),
			$this->y_interval->getRandom()
		];
	}
}