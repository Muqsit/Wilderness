<?php

declare(strict_types=1);

namespace muqsit\wilderness\math;

final class Random2DCoordinateGenerator{

	private ClosedInterval $x_interval;
	private ClosedInterval $y_interval;

	public function __construct(int $minx, int $maxx, int $minz, int $maxz){
		$this->x_interval = ClosedInterval::create($minx, $maxx);
		$this->y_interval = ClosedInterval::create($minz, $maxz);
	}

	/**
	 * @return int[]
	 *
	 * @phpstan-return array{0: int, 1: int}
	 */
	public function generate() : array{
		return [
			$this->x_interval->getRandom(),
			$this->y_interval->getRandom()
		];
	}
}