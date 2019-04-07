<?php

declare(strict_types=1);
namespace muqsit\wilderness\types;

use Ds\Set;

abstract class ListInstance{

	/** @var Set */
	protected $values;

	public function __construct(array $values, ?callable $filter = null){
		if($filter !== null){
			foreach($values as $value){
				if(!$filter($value)){
					throw new \InvalidArgumentException("Could not create list, got invalid list entry: " . $value);
				}
			}
		}

		$this->values = new Set($values);
	}

	abstract public function contains($value) : bool;
}