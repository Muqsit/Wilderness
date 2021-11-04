<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils\lists;

use Closure;
use InvalidArgumentException;

final class Lists{

	public const TYPE_BLACKLIST = "blacklist";
	public const TYPE_WHITELIST = "whitelist";

	/**
	 * @var string[]|ListInstance[]
	 *
	 * @phpstan-var array<string, class-string<ListInstance>>
	 */
	private static array $types = [];

	public static function init() : void{
		self::register(self::TYPE_BLACKLIST, Blacklist::class);
		self::register(self::TYPE_WHITELIST, Whitelist::class);
	}

	/**
	 * @param string $type
	 * @param string $list_class
	 *
	 * @phpstan-param class-string<ListInstance> $list_class
	 */
	public static function register(string $type, string $list_class) : void{
		if(isset(self::$types[$type = strtolower($type)])){
			throw new InvalidArgumentException("List of the type \"{$type}\" is already registered");
		}

		self::$types[$type] = $list_class;
	}

	/**
	 * @param string $type
	 * @param string[] $values
	 * @param Closure|null $validator
	 * @return ListInstance
	 *
	 * @phpstan-param Closure(string) : bool $validator
	 */
	public static function create(string $type, array $values, ?Closure $validator = null) : ListInstance{
		if(!isset(self::$types[$type = strtolower($type)])){
			throw new InvalidArgumentException("Invalid list type \"{$type}\" given. Available list types: " . implode(", ", array_keys(self::$types)));
		}

		return self::$types[$type]::create($values, $validator);
	}
}