<?php

declare(strict_types=1);
namespace muqsit\wilderness\utils;

use pocketmine\utils\TextFormat;

class Language{

	public static function validateEntries(array $entries) : void{
		foreach($entries as $key => $entry){
			if(!is_string($entry)){
				throw new \InvalidArgumentException("Language entry must be a valid string, got " . gettype($entry) . " for key " . $key);
			}
		}
	}

	/** @var string[] */
	private $entries;

	public function __construct(array $entries){
		self::validateEntries($entries);
		array_walk($entries, [TextFormat::class, "colorize"]);
		$this->entries = $entries;
	}

	public function translate(string $key, array $replace_pairs) : string{
		return isset($this->entries[$key]) ? strtr($this->entries[$key], $replace_pairs) : "";
	}
}