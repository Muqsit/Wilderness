<?php

declare(strict_types=1);

namespace muqsit\wilderness\command;

use muqsit\wilderness\behaviour\Behaviour;
use muqsit\wilderness\behaviour\BehaviourTeleportFailReason;
use muqsit\wilderness\session\SessionManager;
use muqsit\wilderness\utils\Position2D;
use muqsit\wilderness\utils\WeakPlayer;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;

final class BehaviourCommandExecutor implements CommandExecutor{

	public function __construct(
		private SessionManager $session_manager,
		private Behaviour $behaviour,
		private bool $do_safe_spawn
	){}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(!($sender instanceof Player)){
			$sender->sendMessage("This command can only be executed as a player.");
			return false;
		}

		if(!$this->session_manager->exists($sender)){
			$this->behaviour->onTeleportFailed($sender, BehaviourTeleportFailReason::AWAITING_LOGIN);
			return false;
		}

		if($this->session_manager->hasCommandLock($sender)){
			$this->behaviour->onTeleportFailed($sender, BehaviourTeleportFailReason::COMMAND_LOCK);
			return false;
		}

		$this->session_manager->setCommandLock($sender);
		$weak_sender = WeakPlayer::from($sender);
		$this->behaviour->generatePosition($sender, function(?Position2D $position) use($weak_sender) : void{
			$sender = $weak_sender->get();
			if($sender === null){
				return;
			}

			if($position === null){
				$this->session_manager->removeCommandLock($sender);
				$this->behaviour->onTeleportFailed($sender, BehaviourTeleportFailReason::CUSTOM);
				return;
			}

			$x_f = (int) floor($position->x);
			$z_f = (int) floor($position->z);
			$position->world->orderChunkPopulation($x_f >> 4, $z_f >> 4, null)->onCompletion(function() use($weak_sender, $position, $x_f, $z_f) : void{
				$sender = $weak_sender->get();
				if($sender !== null){
					$this->session_manager->removeCommandLock($sender);
					$pos = new Vector3($position->x, $position->world->getHighestBlockAt($x_f, $z_f) + 1.0, $position->z);
					if($sender->teleport($position = $this->do_safe_spawn ? $position->world->getSafeSpawn($pos) : Position::fromObject($pos, $position->world))){
						$this->behaviour->onTeleportSuccess($sender, $position);
					}
				}
			}, function() use($weak_sender) : void{
				$sender = $weak_sender->get();
				if($sender !== null){
					$this->session_manager->removeCommandLock($sender);
					$this->behaviour->onTeleportFailed($sender, BehaviourTeleportFailReason::WORLD_CLOSED); // TODO: Figure out other possible causes of failed World::orderChunkPopulation
				}
			});
		});

		return true;
	}
}