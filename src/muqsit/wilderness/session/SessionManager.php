<?php

declare(strict_types=1);

namespace muqsit\wilderness\session;

use muqsit\wilderness\Loader;
use pocketmine\Player;

final class SessionManager{

	/** @var SessionInstance[] */
	private static $sessions = [];

	public static function init(Loader $loader) : void{
		$loader->getServer()->getPluginManager()->registerEvents(new SessionListener(), $loader);
	}

	public static function create(Player $player) : void{
		self::$sessions[$player->getId()] = new SessionInstance();
	}

	public static function destroy(Player $player) : void{
		unset(self::$sessions[$player->getId()]);
	}

	public static function get(Player $player) : SessionInstance{
		return self::$sessions[$player->getId()];
	}

	public static function getNullable(Player $player) : ?SessionInstance{
		return self::$sessions[$player->getId()] ?? null;
	}
}