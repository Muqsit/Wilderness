<?php

declare(strict_types=1);

namespace muqsit\wilderness\session;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

final class SessionListener implements Listener{

	/**
	 * @param PlayerJoinEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		SessionManager::create($event->getPlayer());
	}

	/**
	 * @param PlayerQuitEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerQuit(PlayerQuitEvent $event) : void{
		SessionManager::destroy($event->getPlayer());
	}
}