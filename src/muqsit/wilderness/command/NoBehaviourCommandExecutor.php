<?php

declare(strict_types=1);

namespace muqsit\wilderness\command;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

final class NoBehaviourCommandExecutor implements CommandExecutor{

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		$sender->sendMessage(TextFormat::RED . "No wilderness behaviour was specified.");
		return true;
	}
}