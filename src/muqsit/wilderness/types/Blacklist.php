<?php

declare(strict_types=1);
namespace muqsit\wilderness\types;

class Blacklist extends ListInstance{

	public function contains($value) : bool{
		return !$this->values->contains($value);
	}
}