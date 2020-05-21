<?php

declare(strict_types=1);
namespace muqsit\wilderness\math;

class ClosedInterval{

	/** @var int */
	private $min;

	/** @var int */
	private $max;

	/** @var int */
	private $center;

	/** @var int */
	private $radius;

	public function __construct(int $min, int $max, int $center, int $radius){
		if($min > $max){
			throw new \InvalidArgumentException("The minimum value of the interval (" . $min . ") is greater than the maximum value (" . $max . ")");
		}

		$this->min = $min;
		$this->max = $max;
		$this->center = $center;
		$this->radius = $radius;
	}

	public function getMin() : int{
		return $this->min;
	}

	public function getMax() : int{
		return $this->max;
	}

	public function getRandom() : int{
		if(rand(0, 1) === 1){
			return mt_rand($this->center + $this->radius, $this->max);
		}
		return mt_rand($this->min, $this->center - $this->radius);
	}
}