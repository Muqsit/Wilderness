<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour;

use muqsit\wilderness\Loader;
use pocketmine\utils\Config;

abstract class ConfigurableBehaviour implements Behaviour{

	/** @var string */
	private $config_file_path;

	/** @var Config|null */
	private $config;

	public function init(Loader $loader) : void{
		$config_file_name = BehaviourRegistry::getIdentifier($this);
		$colon_pos = strpos($config_file_name, ":");
		if($colon_pos !== false){
			$config_file_name[$colon_pos] = "-";
		}
		$this->config_file_path = "{$loader->getDataFolder()}/behaviours/{$config_file_name}.yml";
	}

	final protected function getConfig() : Config{
		return $this->config ?? $this->config = new Config($this->config_file_path, Config::YAML);
	}
}