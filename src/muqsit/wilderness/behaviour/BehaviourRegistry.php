<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour;

use InvalidArgumentException;
use InvalidStateException;
use muqsit\wilderness\Loader;
use pocketmine\plugin\Plugin;

final class BehaviourRegistry{

	/** @var bool */
	private static $loader_enabled = false;

	/**
	 * @var Behaviour[]
	 * @phpstan-var array<string, Behaviour>
	 */
	private static $behaviours = [];

	/** @var string[] */
	private static $identifiers = [];

	/**
	 * Registers a new wilderness behaviour.
	 *
	 * WARNING: Attempting to register a behaviour on post plugin enable will
	 * throw an InvalidStateException. Register your custom wilderness
	 * behaviours in your Plugin::onLoad().
	 *
	 * Recommended format for $identifier: lowercase(plugin_name:name)
	 * F.e, Wilderness plugin names its "Default" behaviour "wilderness:default"
	 *
	 * @param string $identifier
	 * @param Behaviour $behaviour
	 */
	public static function register(string $identifier, Behaviour $behaviour) : void{
		if(self::$loader_enabled){
			throw new InvalidArgumentException("Behaviours must be registered during " . Plugin::class . "::onLoad()");
		}

		if(isset(self::$behaviours[$identifier])){
			throw new InvalidStateException("Behaviour with the identifier \"{$identifier}\" already exists");
		}

		self::$behaviours[$identifier] = $behaviour;
		self::$identifiers[spl_object_id($behaviour)] = $identifier;
	}

	public static function getNullable(string $identifier) : ?Behaviour{
		return self::$behaviours[$identifier] ?? null;
	}

	public static function get(string $identifier) : Behaviour{
		return self::$behaviours[$identifier];
	}

	public static function getIdentifier(Behaviour $behaviour) : string{
		return self::$identifiers[spl_object_id($behaviour)];
	}

	public static function onLoaderLoad(Loader $loader) : void{
		$loader->saveResource("behaviours/wilderness-default.yml");
		$name = strtolower($loader->getName());
		self::register("{$name}:default", new DefaultWildernessBehaviour());
	}

	public static function onLoaderEnable(Loader $loader) : void{
		self::$loader_enabled = true;
		foreach(self::$behaviours as $behaviour){
			$behaviour->init($loader);
		}
	}
}