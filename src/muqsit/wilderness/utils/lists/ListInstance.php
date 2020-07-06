<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils\lists;

use Ds\Set;
use InvalidArgumentException;

/**
 * @phpstan-template TType
 */
abstract class ListInstance{

	/**
	 * @var Set
	 * @phpstan-var Set<TType>
	 */
	protected $values;

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

		$this->values = new Set($values);
	}

	/**
	 * @return Set
	 *
	 * @phpstan-return Set<TType>
	 */
	public function getValues() : Set{
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