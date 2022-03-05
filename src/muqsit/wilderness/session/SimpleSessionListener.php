<?php

declare(strict_types=1);

namespace muqsit\wilderness\session;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

final class SimpleSessionListener implements Listener{

	public function __construct(
		private SimpleSessionManager $manager
	){}

	/**
	 * @param PlayerJoinEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$this->manager->create($event->getPlayer());
	}

	/**
	 * @param PlayerQuitEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		$this->manager->destroy($event->getPlayer());
	}
}