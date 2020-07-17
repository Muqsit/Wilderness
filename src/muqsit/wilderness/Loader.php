<?php

declare(strict_types=1);

namespace muqsit\wilderness;

use InvalidArgumentException;
use muqsit\wilderness\behaviour\Behaviour;
use muqsit\wilderness\behaviour\BehaviourRegistry;
use muqsit\wilderness\command\BehaviourCommandExecutor;
use muqsit\wilderness\command\NoBehaviourCommandExecutor;
use muqsit\wilderness\session\SessionManager;
use muqsit\wilderness\utils\lists\Lists;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\PluginBase;

final class Loader extends PluginBase{

	/** @var bool */
	private $chunk_load_flood_protection;

	/** @var bool */
	private $do_safe_spawn;

	/** @var PluginCommand */
	private $command;

	/** @var Behaviour|null */
	private $behaviour;

	protected function onLoad() : void{
		Lists::init();
		BehaviourRegistry::onLoaderLoad($this);
	}

	protected function onEnable() : void{
		$this->saveDefaultConfig();

		$command = new PluginCommand("wilderness", $this, new NoBehaviourCommandExecutor());
		$command->setAliases(["wild"]);
		$command->setPermission("wilderness.command");
		$this->getServer()->getCommandMap()->register($this->getName(), $command);
		$this->command = $command;

		$this->chunk_load_flood_protection = (bool) $this->getConfig()->get("chunk-load-flood-protection");
		$this->do_safe_spawn = (bool) $this->getConfig()->get("do-safe-spawn");

		if($this->chunk_load_flood_protection){
			SessionManager::init($this);
		}

		$behaviour_identifier = $this->getConfig()->get("behaviour");
		if($behaviour_identifier !== false){
			$behaviour = BehaviourRegistry::getNullable($this->getConfig()->get("behaviour"));
			if($behaviour === null){
				throw new InvalidArgumentException("Unregistered behaviour type: \"{$this->getConfig()->get("behaviour")}\"");
			}
			$this->setBehaviour($behaviour);
		}
	}

	public function getBehaviour() : ?Behaviour{
		return $this->behaviour;
	}

	public function setBehaviour(?Behaviour $behaviour) : bool{
		if($this->behaviour !== $behaviour){
			$this->behaviour = $behaviour;
			if($this->behaviour !== null){
				$this->behaviour->select($this);
				$this->command->setExecutor(new BehaviourCommandExecutor($this->behaviour, $this->chunk_load_flood_protection, $this->do_safe_spawn));
			}else{
				$this->command->setExecutor(new NoBehaviourCommandExecutor());
			}
			return true;
		}
		return false;
	}
}