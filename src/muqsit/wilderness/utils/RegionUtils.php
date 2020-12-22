<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils;

use pocketmine\world\World;

final class RegionUtils{

	public static function onChunkGenerate(World $world, int $chunkX, int $chunkZ, callable $callback) : void{
		if($world->isChunkPopulated($chunkX, $chunkZ)){
			$callback();
			return;
		}

		$loader_listener = new PopulatedChunkListener($world, $chunkX, $chunkZ, $callback);
		$world->registerChunkListener($loader_listener, $chunkX, $chunkZ);
		$world->registerChunkLoader($loader_listener, $chunkX, $chunkZ, true);
		$world->orderChunkPopulation($chunkX, $chunkZ);
	}
}