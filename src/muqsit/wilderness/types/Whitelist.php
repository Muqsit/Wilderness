<?php

declare(strict_types=1);
namespace muqsit\wilderness\types;

class Whitelist extends ListInstance{

	public function contains($value) : bool{
		return $this->values->contains($value);
	}
}