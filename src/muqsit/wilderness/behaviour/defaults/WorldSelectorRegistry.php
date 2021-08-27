<?php

declare(strict_types=1);

namespace muqsit\wilderness\behaviour\defaults;

final class WorldSelectorRegistry{

	/**
	 * @var string[]|WorldSelector[]
	 * @phpstan-var array<string, class-string<WorldSelector>>
	 */
	private static array $selectors = [];

	public static function registerDefaults() : void{
		self::register("self", SelfWorldSelector::class);
		self::register("random", RandomWorldSelector::class);
		self::register("specific", SpecificWorldSelector::class);
	}

	/**
	 * @param string $type
	 * @param string $class
	 *
	 * @phpstan-param class-string<WorldSelector> $class
	 */
	public static function register(string $type, string $class) : void{
		self::$selectors[$type] = $class;
	}

	/**
	 * @param string $type
	 * @param mixed[] $data
	 * @return WorldSelector
	 */
	public static function get(string $type, array $data) : WorldSelector{
		return self::$selectors[$type]::fromConfiguration($data);
	}
}