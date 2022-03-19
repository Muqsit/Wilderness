<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils;

use InvalidArgumentException;
use pocketmine\command\CommandSender;

final class Language{

	/**
	 * @param mixed[] $entries
	 * @return Language
	 *
	 * @phpstan-param array<string, string> $entries
	 */
	public static function create(array $entries) : self{
		foreach($entries as $key => $entry){
			if(!is_string($entry)){
				throw new InvalidArgumentException("Language entry must be a valid string, got " . gettype($entry) . " for key {$key}");
			}
		}

		return new self($entries);
	}

	/**
	 * @param string[] $entries
	 *
	 * @phpstan-param array<string, string> $entries
	 */
	private function __construct(
		private array $entries
	){}

	/**
	 * @param string $key
	 * @param string[] $replace_pairs
	 * @return string|null
	 *
	 * @phpstan-param array<string, string> $replace_pairs
	 */
	public function translate(string $key, array $replace_pairs = []) : ?string{
		$result = isset($this->entries[$key]) ? strtr($this->entries[$key], $replace_pairs) : "";
		return $result !== "" ? $result : null;
	}

	/**
	 * @param CommandSender $messagable
	 * @param string $key
	 * @param string[] $replace_pairs
	 * @return bool
	 *
	 * @phpstan-param array<string, string> $replace_pairs
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