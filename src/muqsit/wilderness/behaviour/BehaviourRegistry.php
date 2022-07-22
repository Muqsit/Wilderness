<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour;

use InvalidArgumentException;
use muqsit\wilderness\behaviour\defaults\WorldSelectorRegistry;
use muqsit\wilderness\Loader;

final class BehaviourRegistry{

	/** @var array<string, Behaviour> */
	private static array $behaviours = [];

	/** @var array<int, string> */
	private static array $identifiers = [];

	/**
	 * Registers a new wilderness behaviour.
	 *
	 * WARNING: Attempting to register a behaviour on post plugin enable will
	 * throw an InvalidArgumentException. Register your custom wilderness
	 * behaviours in your Plugin::onLoad().
	 *
	 * Recommended format for $identifier: lowercase(plugin_name:name)
	 * F.e, Wilderness plugin names its "Default" behaviour "wilderness:default"
	 *
	 * @param string $identifier
	 * @param Behaviour $behaviour
	 */
	public static function register(string $identifier, Behaviour $behaviour) : void{
		if(isset(self::$behaviours[$identifier])){
			throw new InvalidArgumentException("Behaviour with the identifier \"{$identifier}\" already exists");
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
		$directory = $loader->getDataFolder() . "behaviours";
		if(!is_dir($directory)){
			/** @noinspection MkdirRaceConditionInspection */
			mkdir($directory);
		}

		WorldSelectorRegistry::registerDefaults();
		$name = strtolower($loader->getName());
		self::register("{$name}:default", new DefaultWildernessBehaviour());
	}
}