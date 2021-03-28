<?php

declare(strict_types=1);

namespace muqsit\wilderness\session;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

final class SimpleSessionListener implements Listener{

	private SimpleSessionManager $manager;

	public function __construct(SimpleSessionManager $manager){
		$this->manager = $manager;
	}

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