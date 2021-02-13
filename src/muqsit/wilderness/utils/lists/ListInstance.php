<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils\lists;

use InvalidArgumentException;

abstract class ListInstance{

	/**
	 * @var string[]
	 *
	 * @phpstan-var array<string, string>
	 */
	protected $values = [];

	/**
	 * @param string[] $values
	 * @param callable|null $validator
	 *
	 * @phpstan-param string[] $values
	 */
	public function __construct(array $values, ?callable $validator = null){
		foreach($values as $value){
			if($validator !== null && !$validator($value)){
				throw new InvalidArgumentException("Could not create list, got invalid list entry: {$value}");
			}
			$this->values[$value] = $value;
		}
	}

	/**
	 * @return string[]
	 */
	public function getValues() : array{
		return array_keys($this->values);
	}

	abstract public function contains(string $value) : bool;
}