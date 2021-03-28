<?php

declare(strict_types=1);

namespace muqsit\wilderness\command;

use muqsit\wilderness\behaviour\Behaviour;
use muqsit\wilderness\behaviour\BehaviourTeleportFailReason;
use muqsit\wilderness\session\SessionManager;
use muqsit\wilderness\utils\Position2D;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;

final class BehaviourCommandExecutor implements CommandExecutor{

	/** @var Behaviour */
	private $behaviour;

	/** @var bool */
	private $chunk_load_flood_protection;

	/** @var bool */
	private $do_safe_spawn;

	public function __construct(Behaviour $behaviour, bool $chunk_load_flood_protection, bool $do_safe_spawn){
		$this->behaviour = $behaviour;
		$this->chunk_load_flood_protection = $chunk_load_flood_protection;
		$this->do_safe_spawn = $do_safe_spawn;
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(!($sender instanceof Player)){
			$sender->sendMessage("This command can only be executed as a player.");
			return false;
		}

		if($this->chunk_load_flood_protection){
			$session = SessionManager::getNullable($sender);
			if($session === null){
				$this->behaviour->onTeleportFailed($sender, BehaviourTeleportFailReason::AWAITING_LOGIN);
				return false;
			}

			if($session->hasCommandLock()){
				$this->behaviour->onTeleportFailed($sender, BehaviourTeleportFailReason::COMMAND_LOCK);
				return false;
			}

			$session->setCommandLock(true);
		}

		$this->behaviour->generatePosition($sender, function(?Position2D $position) use($sender) : void{
			if($sender->isOnline()){
				if($position === null){
					if($this->chunk_load_flood_protection){
						SessionManager::get($sender)->setCommandLock(false);
					}
					$this->behaviour->onTeleportFailed($sender, BehaviourTeleportFailReason::CUSTOM);
					return;
				}

				$x_f = (int) floor($position->x);
				$z_f = (int) floor($position->z);
				$position->world->orderChunkPopulation($x_f >> 4, $z_f >> 4, null)->onCompletion(
					function() use($sender, $position, $x_f, $z_f) : void{
						if($sender->isOnline()){
							if($this->chunk_load_flood_protection){
								SessionManager::get($sender)->setCommandLock(false);
							}

							if($position->world->isClosed()){
								$this->behaviour->onTeleportFailed($sender, BehaviourTeleportFailReason::WORLD_CLOSED);
								return;
							}

							$pos = new Vector3($position->x, $position->world->getHighestBlockAt($x_f, $z_f) + 1.0, $position->z);
							if($sender->teleport($position = $this->do_safe_spawn ? $position->world->getSafeSpawn($pos) : Position::fromObject($pos, $position->world))){
								$this->behaviour->onTeleportSuccess($sender, $position);
							}
						}
					},
					static function() : void{ /* failure */ }
				);
			}
		});

		return true;
	}
}