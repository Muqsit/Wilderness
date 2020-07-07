<?php

declare(strict_types=1);

namespace muqsit\wilderness;

use InvalidArgumentException;
use muqsit\wilderness\behaviour\Behaviour;
use muqsit\wilderness\behaviour\BehaviourRegistry;
use muqsit\wilderness\behaviour\BehaviourTeleportFailReason;
use muqsit\wilderness\session\SessionManager;
use muqsit\wilderness\utils\lists\Lists;
use muqsit\wilderness\utils\Position2D;
use muqsit\wilderness\utils\RegionUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\world\Position;

final class Loader extends PluginBase{

	/** @var bool */
	private $chunk_load_flood_protection;

	/** @var bool */
	private $do_safe_spawn;

	/** @var Behaviour */
	private $behaviour;

	protected function onLoad() : void{
		Lists::init();
		BehaviourRegistry::onLoaderLoad($this);
	}

	protected function onEnable() : void{
		$this->saveDefaultConfig();

		$this->chunk_load_flood_protection = (bool) $this->getConfig()->get("chunk-load-flood-protection");
		$this->do_safe_spawn = (bool) $this->getConfig()->get("do-safe-spawn");

		if($this->chunk_load_flood_protection){
			SessionManager::init($this);
		}

		BehaviourRegistry::onLoaderEnable($this);
		$behaviour = BehaviourRegistry::getNullable($this->getConfig()->get("behaviour"));
		if($behaviour === null){
			throw new InvalidArgumentException("Unregistered behaviour type: \"{$this->getConfig()->get("behaviour")}\"");
		}
		$this->behaviour = $behaviour;
	}

	public function getBehaviour() : Behaviour{
		return $this->behaviour;
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
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
					$this->behaviour->onTeleportFailed($sender, BehaviourTeleportFailReason::CUSTOM);
					return;
				}

				$x_f = (int) floor($position->x);
				$z_f = (int) floor($position->z);
				RegionUtils::onChunkGenerate(
					$position->world, $x_f >> 4, $z_f >> 4,
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
					}
				);
			}
		});

		return true;
	}
}