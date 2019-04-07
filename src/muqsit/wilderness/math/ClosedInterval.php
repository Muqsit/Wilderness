<?php

declare(strict_types=1);
namespace muqsit\wilderness\math;

class ClosedInterval{

	/** @var int */
	private $min;

	/** @var int */
	private $max;

	public function __construct(int $min, int $max){
		if($min > $max){
			throw new \InvalidArgumentException("The minimum value of the interval (" . $min . ") is greater than the maximum value (" . $max . ")");
		}

		$this->min = $min;
		$this->max = $max;
	}

	public function getMin() : int{
		return $this->min;
	}

	public function getMax() : int{
		return $this->max;
	}

	public function getRandom() : int{
		return mt_rand($this->min, $this->max);
	}
}