<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils;

use pocketmine\player\Player;
use pocketmine\Server;
use Ramsey\Uuid\UuidInterface;

final class WeakPlayer{

	public static function from(Player $player) : self{
		return new self($player->getUniqueId(), $player->getId());
	}

	private function __construct(
		private UuidInterface $uuid,
		private int $entity_runtime_id
	){}

	public function get() : ?Player{
		$player = Server::getInstance()->getPlayerByUUID($this->uuid);
		return $player !== null && $player->getId() === $this->entity_runtime_id ? $player : null;
	}
}