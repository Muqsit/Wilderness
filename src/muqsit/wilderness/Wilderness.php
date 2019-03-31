<?php

declare(strict_types=1);
namespace muqsit\wilderness;

use muqsit\wilderness\utils\RegionUtils;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;

class Wilderness extends PluginBase{

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
		$level = $sender->getLevel();
		$x = mt_rand(-10000, 10000);
		$z = mt_rand(-10000, 10000);

		$chunkX = $x >> 4;
		$chunkZ = $z >> 4;

		$sender->sendMessage("Teleporting...");
		RegionUtils::onChunkGenerate($level, $chunkX, $chunkZ, function() use($sender, $x, $z, $level) : void{
			if($sender->isOnline()){
				$y = $level->getHighestBlockAt($x, $z) + 1;
				$sender->teleport(Position::fromObject(new Vector3($x, $y, $z), $level));
				$sender->sendMessage("Teleported!");
			}
		});

		return true;
	}
}