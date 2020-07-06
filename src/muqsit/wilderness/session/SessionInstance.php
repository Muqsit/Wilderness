<?php

declare(strict_types=1);

namespace muqsit\wilderness\session;

final class SessionInstance{

	/** @var bool */
	private $command_lock = false;

	public function hasCommandLock() : bool{
		return $this->command_lock;
	}

	public function setCommandLock(bool $value) : void{
		$this->command_lock = $value;
	}
}