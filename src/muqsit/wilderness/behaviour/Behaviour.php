<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour;

use Closure;
use muqsit\wilderness\Loader;
use muqsit\wilderness\utils\Position2D;
use pocketmine\player\Player;
use pocketmine\world\Position;

interface Behaviour{

	/**
	 * Called when this behaviour is selected.
	 *
	 * @param Loader $loader
	 */
	public function select(Loader $loader) : void;

	/**
	 * Generate a 2D position (horizontal axis) for a given Player
	 * instance. RegionUtils will take care of the Y axis.
	 *
	 * Return null in closure to not teleport the player.
	 *
	 * @param Player $player
	 * @param Closure(?Position2D) : void $callback
	 */
	public function generatePosition(Player $player, Closure $callback) : void;

	/**
	 * Called when teleportation fails.
	 * @see BehaviourTeleportFailReason
	 *
	 * @param Player $player
	 * @param BehaviourTeleportFailReason::* $reason
	 */
	public function onTeleportFailed(Player $player, int $reason) : void;

	/**
	 * Called after the player has successfully been teleported to
	 * a generated position.
	 *
	 * @param Player $player
	 * @param Position $position
	 */
	public function onTeleportSuccess(Player $player, Position $position) : void;
}