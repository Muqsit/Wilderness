<?php

declare(strict_types=1);
namespace muqsit\wilderness\utils;

use pocketmine\level\Level;

final class RegionUtils{

	public static function onChunkGenerate(Level $level, int $chunkX, int $chunkZ, callable $callback) : void{
		if($level->isChunkPopulated($chunkX, $chunkZ)){
			$callback();
			return;
		}

		$level->registerChunkLoader(new PopulatedChunkListener($level, $chunkX, $chunkZ, $callback), $chunkX, $chunkZ, true);
	}
}