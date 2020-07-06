<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils;

use InvalidArgumentException;
use pocketmine\command\CommandSender;

class Language{

	/**
	 * @param mixed[] $entries
	 */
	public static function validateEntries(array $entries) : void{
		foreach($entries as $key => $entry){
			if(!is_string($entry)){
				throw new InvalidArgumentException("Language entry must be a valid string, got " . gettype($entry) . " for key {$key}");
			}
		}
	}

	/** @var string[] */
	private $entries;

	/**
	 * @param array<string, string> $entries
	 */
	public function __construct(array $entries){
		self::validateEntries($entries);
		$this->entries = $entries;
	}

	/**
	 * @param string $key
	 * @param array<string, string> $replace_pairs
	 * @return string|null
	 */
	public function translate(string $key, array $replace_pairs = []) : ?string{
		$result = isset($this->entries[$key]) ? strtr($this->entries[$key], $replace_pairs) : "";
		return $result !== "" ? $result : null;
	}

	/**
	 * @param CommandSender $messagable
	 * @param string $key
	 * @param array<string, string> $replace_pairs
	 * @return bool
	 */
	public function translateAndSend(CommandSender $messagable, string $key, array $replace_pairs = []) : bool{
		$translated = $this->translate($key, $replace_pairs);
		if($translated !== null){
			$messagable->sendMessage($translated);
			return true;
		}
		return false;
	}
}