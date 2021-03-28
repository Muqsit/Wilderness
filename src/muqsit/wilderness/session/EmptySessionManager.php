<?php

declare(strict_types=1);

namespace muqsit\wilderness\session;

use pocketmine\player\Player;

final class EmptySessionManager implements SessionManager{

	public function exists(Player $player) : bool{
		return true;
	}

	public function hasCommandLock(Player $player) : bool{
		return false;
	}

	public function setCommandLock(Player $player) : void{
	}

	public function removeCommandLock(Player $player) : void{
	}
}