<?php

declare(strict_types=1);

namespace muqsit\wilderness\session;

use pocketmine\player\Player;

/**
 * @internal
 */
interface SessionManager{

	public function exists(Player $player) : bool;

	public function hasCommandLock(Player $player) : bool;

	public function setCommandLock(Player $player) : void;

	public function removeCommandLock(Player $player) : void;
}