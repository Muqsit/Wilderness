<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils\lists;

/**
 * @phpstan-template TType
 * @phpstan-extends ListInstance<TType>
 */
class Whitelist extends ListInstance{

	public function contains($value) : bool{
		return isset($this->values[$value]);
	}
}