<?php

declare(strict_types=1);

namespace muqsit\wilderness;

use InvalidArgumentException;
use muqsit\wilderness\behaviour\Behaviour;
use muqsit\wilderness\behaviour\BehaviourRegistry;
use muqsit\wilderness\command\BehaviourCommandExecutor;
use muqsit\wilderness\command\NoBehaviourCommandExecutor;
use muqsit\wilderness\session\EmptySessionManager;
use muqsit\wilderness\session\SessionManager;
use muqsit\wilderness\session\SimpleSessionManager;
use muqsit\wilderness\utils\lists\Lists;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\PluginBase;

final class Loader extends PluginBase{

	private bool $do_safe_spawn;
	private PluginCommand $command;
	private ?Behaviour $behaviour = null;
	private SessionManager $session_manager;

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

		$this->do_safe_spawn = (bool) $this->getConfig()->get("do-safe-spawn");
		$this->session_manager = (bool) $this->getConfig()->get("chunk-load-flood-protection") ? new SimpleSessionManager($this) : new EmptySessionManager();

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
				$this->command->setExecutor(new BehaviourCommandExecutor($this->session_manager, $this->behaviour, $this->do_safe_spawn));
			}else{
				$this->command->setExecutor(new NoBehaviourCommandExecutor());
			}
			return true;
		}
		return false;
	}
}