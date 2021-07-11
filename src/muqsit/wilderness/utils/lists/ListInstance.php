<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils\lists;

use InvalidArgumentException;

/**
 * @phpstan-template TType
 */
abstract class ListInstance{

	/**
	 * @var array<mixed, mixed>
	 * @phpstan-var array<TType, TType>
	 */
	protected $values = [];

	/**
	 * @param mixed[] $values
	 * @param callable|null $validator
	 *
	 * @phpstan-param TType[] $values
	 */
	public function __construct(array $values, ?callable $validator = null){
		if($validator !== null){
			foreach($values as $value){
				if(!$validator($value)){
					throw new InvalidArgumentException("Could not create list, got invalid list entry: {$value}");
				}
			}
		}

		foreach($values as $value){
			$this->values[$value] = $value;
		}
	}

	/**
	 * @return array<mixed, mixed>
	 *
	 * @phpstan-return array<TType, TType>
	 */
	public function getValues() : array{
		return $this->values;
	}

	/**
	 * @param mixed $value
	 * @return bool
	 *
	 * @phpstan-param TType $value
	 */
	abstract public function contains($value) : bool;
}