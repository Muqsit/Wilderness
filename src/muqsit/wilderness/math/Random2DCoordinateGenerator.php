<?php

declare(strict_types=1);
namespace muqsit\wilderness\math;

class Random2DCoordinateGenerator{

	public static function validateIntervalArray(array $interval) : void{
		if(count($interval) !== 2){
			throw new \InvalidArgumentException("Interval should contain exactly two elements, got " . count($interval));
		}

		foreach($interval as $element){
			if(!is_int($element)){
				throw new \InvalidArgumentException("Interval endpoint must be an integer, got " . gettype($element) . " (" . $element . ")");
			}
		}
	}

	/** @var int[] */
	private $x_interval;

	/** @var int[] */
	private $y_interval;

	public function __construct(int $minx, int $maxx, int $minz, int $maxz){
		$this->x_interval = new ClosedInterval($minx, $maxx);
		$this->y_interval = new ClosedInterval($minz, $maxz);
	}

	public function generate() : array{
		return [
			$this->x_interval->getRandom(),
			$this->y_interval->getRandom()
		];
	}
}