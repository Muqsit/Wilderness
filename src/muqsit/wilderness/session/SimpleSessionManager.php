<?php

declare(strict_types=1);

namespace muqsit\wilderness\session;

use muqsit\wilderness\Loader;
use pocketmine\player\Player;

final class SimpleSessionManager implements SessionManager{

	/** @var SessionInstance[] */
	private array $sessions = [];

	public function __construct(Loader $loader){
		$loader->getServer()->getPluginManager()->registerEvents(new SimpleSessionListener($this), $loader);
	}

	public function create(Player $player) : void{
		$this->sessions[$player->getId()] = new SessionInstance();
	}

	public function destroy(Player $player) : void{
		unset($this->sessions[$player->getId()]);
	}

	public function exists(Player $player) : bool{
		return isset($this->sessions[$player->getId()]);
	}

	public function hasCommandLock(Player $player) : bool{
		return $this->sessions[$player->getId()]->command_lock;
	}

	public function setCommandLock(Player $player) : void{
		if(isset($this->sessions[$id = $player->getId()])){
			$this->sessions[$id]->command_lock = true;
		}
	}

	public function removeCommandLock(Player $player) : void{
		if(isset($this->sessions[$id = $player->getId()])){
			$this->sessions[$id]->command_lock = false;
		}
	}
}