<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils\lists;

use Closure;
use InvalidArgumentException;

abstract class ListInstance{

	/**
	 * @param string[] $values
	 * @param (Closure(string) : bool)|null $validator
	 * @return ListInstance
	 */
	final public static function create(array $values, ?Closure $validator = null) : self{
		$valid_values = [];
		foreach($values as $value){
			if($validator !== null && !$validator($value)){
				throw new InvalidArgumentException("Could not create list, got invalid list entry: {$value}");
			}
			$valid_values[$value] = $value;
		}

		return new static($valid_values);
	}

	/**
	 * @param array<string, string> $values
	 */
	final private function __construct(
		protected array $values
	){}

	/**
	 * @return string[]
	 */
	final public function getValues() : array{
		return array_keys($this->values);
	}

	abstract public function contains(string $value) : bool;
}