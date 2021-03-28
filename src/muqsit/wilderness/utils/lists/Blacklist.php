<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils\lists;

final class Blacklist extends ListInstance{

	public function contains(string $value) : bool{
		return !isset($this->values[$value]);
	}
}