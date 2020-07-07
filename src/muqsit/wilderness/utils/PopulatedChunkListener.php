<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils;

use pocketmine\math\Vector3;
use pocketmine\world\ChunkListener;
use pocketmine\world\ChunkLoader;
use pocketmine\world\format\Chunk;
use pocketmine\world\World;

class PopulatedChunkListener implements ChunkLoader, ChunkListener{

	/** @var World */
	private $world;

	/** @var int */
	private $x;

	/** @var int */
	private $z;

	/** @var callable */
	private $callback;

	public function __construct(World $world, int $chunkX, int $chunkZ, callable $callback){
		$this->world = $world;
		$this->x = $chunkX;
		$this->z = $chunkZ;
		$this->callback = $callback;
	}

	private function onComplete() : void{
		$this->world->unregisterChunkListener($this, $this->x, $this->z);
		$this->world->unregisterChunkLoader($this, $this->x, $this->z);
		($this->callback)();
	}

	public function getX() : float{
		return (float) $this->x;
	}

	public function getZ() : float{
		return (float) $this->z;
	}

	public function onChunkLoaded(Chunk $chunk) : void{
		if(!$chunk->isPopulated()){
			$this->world->populateChunk($this->x, $this->z);
		}else{
			$this->onComplete();
		}
	}

	public function onChunkPopulated(Chunk $chunk) : void{
		$this->onComplete();
	}

	public function onChunkChanged(Chunk $chunk) : void{}
	public function onChunkUnloaded(Chunk $chunk) : void{}
	public function onBlockChanged(Vector3 $block) : void{}
}