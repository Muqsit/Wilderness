<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour;

final class BehaviourTeleportFailReason{

	/**
	 * Caused when a player executed /wild too early,
	 * i.e, SessionHandler has not registered them.
	 */
	public const AWAITING_LOGIN = 0x01;

	/**
	 * Caused when a player executes /wild when have already
	 * executed a /wild that's still processing.
	 */
	public const COMMAND_LOCK = 0x02;

	/**
	 * Caused when a world closes when waiting for terrain
	 * generation.
	 */
	public const WORLD_CLOSED = 0x03;

	/**
	 * Caused when a behaviour returns null when requested to
	 * generate a position.
	 *
	 * @see Behaviour::generatePosition()
	 */
	public const CUSTOM = 0x04;

	private function __construct(){
	}
}