<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour;

use InvalidArgumentException;
use muqsit\wilderness\utils\Language;
use muqsit\wilderness\utils\lists\ListInstance;
use muqsit\wilderness\utils\lists\Lists;
use pocketmine\utils\TextFormat;

final class BehaviourHelper{

	/**
	 * Parses gray list from a user-friendly configuration.
	 *
	 * 		< Configuration Format >
	 * $type = string E {"blacklist", "whitelist"}
	 * $list = string[]
	 * $configuration = ["type" => $type, "list" => $list]
	 *
	 * @param array<string, mixed> $configuration
	 * @return ListInstance
	 */
	public static function parseGrayList(array $configuration) : ListInstance{
		if(!isset($configuration["type"])){
			throw new InvalidArgumentException("Configuration must specify the type of list in \"type\"");
		}

		if(!isset($configuration["list"])){
			throw new InvalidArgumentException("Configuration must specify the entries in the list in \"list\"");
		}

		return Lists::create($configuration["type"], $configuration["list"]);
	}

	/**
	 * Parses language from a user-friendly configuration.
	 *
	 * 		< Configuration Format >
	 * $key_1, ...$key_n = string
	 * $value_1, ...$value_n = string|string[]
	 * $configuration = [$key_1 => $value_1, ...$key_n => $value_n]
	 *
	 * Values may contain colour codes prefixed with "§" or "&".
	 * If value is a string[], all entries in the array will be
	 * joined using TextFormat::EOL.
	 *
	 * @param array<string, string|array<string>> $configuration
	 * @return Language
	 */
	public static function parseLanguage(array $configuration) : Language{
		$entries = [];

		/**
		 * @var string $key
		 * @var string|string[] $value
		 */
		foreach($configuration as $key => $value){
			$entries[$key] = TextFormat::colorize(is_array($value) ? implode(TextFormat::EOL, $value) : $value);
		}

		return Language::create($entries);
	}
}