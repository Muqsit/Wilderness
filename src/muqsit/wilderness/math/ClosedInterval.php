<?php

declare(strict_types=1);

namespace muqsit\wilderness\math;

use InvalidArgumentException;

final class ClosedInterval{

	public static function create(int $min, int $max) : self{
		if($min > $max){
			throw new InvalidArgumentException("The minimum value of the interval ({$min}) is greater than the maximum value ({$max})");
		}

		return new self($min, $max);
	}

	private int $min;
	private int $max;

	private function __construct(int $min, int $max){
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