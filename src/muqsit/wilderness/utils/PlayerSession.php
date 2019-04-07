<?php

declare(strict_types=1);
namespace muqsit\wilderness\utils;

use pocketmine\Player;

class PlayerSession{

	/** @var PlayerSession[] */
	private static $sessions = [];

	public static function create(Player $player) : void{
		self::$sessions[$player->getId()] = new PlayerSession();
	}

	public static function destroy(Player $player) : void{
		unset(self::$sessions[$player->getId()]);
	}

	public static function get(Player $player) : ?PlayerSession{
		return self::$sessions[$player->getId()] ?? null;
	}

	/** @var bool */
	private $command_lock = false;

	private function __construct(){
	}

	public function hasCommandLock() : bool{
		return $this->command_lock;
	}

	public function setCommandLock(bool $value) : void{
		$this->command_lock = $value;
	}
}