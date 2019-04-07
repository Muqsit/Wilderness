<?php

declare(strict_types=1);
namespace muqsit\wilderness\types;

use pocketmine\utils\Utils;

final class Lists{

	public const TYPE_BLACKLIST = "blacklist";
	public const TYPE_WHITELIST = "whitelist";

	/** @var string[] */
	private static $types = [];

	public static function init() : void{
		self::register(self::TYPE_BLACKLIST, Blacklist::class);
		self::register(self::TYPE_WHITELIST, Whitelist::class);
	}

	public static function register(string $type, string $list_class) : void{
		if(isset(self::$types[$type = strtolower($type)])){
			throw new \InvalidArgumentException("List of the type " . $type . " is already registered");
		}

		Utils::testValidInstance($list_class, ListInstance::class);
		self::$types[$type] = $list_class;
	}

	public static function create(string $type, ...$args) : ListInstance{
		if(!isset(self::$types[$type = strtolower($type)])){
			throw new \InvalidArgumentException("Invalid list type " . $type . " given. Available list types: " . implode(", ", array_keys(self::$types)));
		}

		return new self::$types[$type](...$args);
	}
}