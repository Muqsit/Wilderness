<?php

declare(strict_types=1);

namespace muqsit\wilderness\utils;

use pocketmine\level\ChunkLoader;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class PopulatedChunkListener implements ChunkLoader{

	/** @var Position */
	private $position;

	/** @var int */
	private $x;

	/** @var int */
	private $z;

	/** @var int */
	private $loaderId = 0;

	/** @var callable */
	private $callback;

	public function __construct(Level $world, int $chunkX, int $chunkZ, callable $callback){
		$this->position = Position::fromObject(new Vector3($chunkX << 4, $chunkZ << 4), $world);
		$this->x = $chunkX;
		$this->z = $chunkZ;
		$this->loaderId = Level::generateChunkLoaderId($this);
		$this->callback = $callback;
	}

	public function onChunkLoaded(Chunk $chunk) : void{
		if(!$chunk->isPopulated()){
			$this->getLevel()->populateChunk($this->x, $this->z);
			return;
		}

		$this->onComplete();
	}

	public function onChunkPopulated(Chunk $chunk) : void{
		$this->onComplete();
	}

	private function onComplete() : void{
		$this->getLevel()->unregisterChunkLoader($this, $this->x, $this->z);
		($this->callback)();
	}

	public function getLoaderId() : int{
		return $this->loaderId;
	}

	public function isLoaderActive() : bool{
		return true;
	}

	public function getPosition() : Position{
		return $this->position;
	}

	public function getLevel() : Level{
		return $this->position->getLevelNonNull();
	}

	public function getX() : float{
		return (float) $this->x;
	}

	public function getZ() : float{
		return (float) $this->z;
	}

	public function onChunkChanged(Chunk $chunk) : void{
	}

	public function onChunkUnloaded(Chunk $chunk) : void{
	}

	public function onBlockChanged(Vector3 $block) : void{
	}
}