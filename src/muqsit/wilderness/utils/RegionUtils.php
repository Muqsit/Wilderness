<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils;

use pocketmine\level\Level;

final class RegionUtils{

	public static function onChunkGenerate(Level $world, int $chunkX, int $chunkZ, callable $callback) : void{
		if($world->isChunkPopulated($chunkX, $chunkZ)){
			$callback();
			return;
		}

		$world->registerChunkLoader(new PopulatedChunkListener($world, $chunkX, $chunkZ, $callback), $chunkX, $chunkZ, true);
	}
}