<?php

declare(strict_types=1);

namespace muqsit\wilderness\command;

use Closure;
use Generator;
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
use pocketmine\world\format\Chunk;
use pocketmine\world\Position;
use RuntimeException;
use SOFe\AwaitGenerator\Await;
use function floor;

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
		Await::f2c(function() use($weak_sender) : Generator{
			try{
				$position = yield from Await::promise(function(Closure $resolve, Closure $reject) use($weak_sender) : void{
					$sender = $weak_sender->get();
					$sender !== null ? $this->behaviour->generatePosition($sender, $resolve) : $reject(new RuntimeException("Player is offline"));
				});
			}catch(RuntimeException){
				return;
			}

			if($position === null){
				$sender = $weak_sender->get();
				if($sender !== null){
					$this->session_manager->removeCommandLock($sender);
					$this->behaviour->onTeleportFailed($sender, BehaviourTeleportFailReason::CUSTOM);
				}
				return;
			}

			$x = (int) floor($position->x);
			$z = (int) floor($position->z);
			$world = $position->world;
			try{
				yield from Await::promise(static function(Closure $resolve, Closure $reject) use($x, $z, $world) : void{
					$world->isLoaded() ?
						$world->orderChunkPopulation($x >> Chunk::COORD_BIT_SIZE, $z >> Chunk::COORD_BIT_SIZE, null)->onCompletion($resolve, static function() use($reject) : void{ $reject(new RuntimeException("Failed to load chunk")); }) :
						$reject(new RuntimeException("World is closed"));
				});
			}catch(RuntimeException){
				$sender = $weak_sender->get();
				if($sender !== null){
					$this->session_manager->removeCommandLock($sender);
					$this->behaviour->onTeleportFailed($sender, BehaviourTeleportFailReason::WORLD_CLOSED); // TODO: Figure out other possible causes of failed World::orderChunkPopulation
				}
				return;
			}

			$sender = $weak_sender->get();
			if($sender !== null){
				$this->session_manager->removeCommandLock($sender);
				$pos = new Vector3($position->x, $world->getHighestBlockAt($x, $z) + 1.0, $position->z);
				if($sender->teleport($position = $this->do_safe_spawn ? $world->getSafeSpawn($pos) : Position::fromObject($pos, $world))){
					$this->behaviour->onTeleportSuccess($sender, $position);
				}
			}
		});
		return true;
	}
}