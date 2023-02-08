<?php

namespace ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

use ReformedDevs\Prisons_Core\Core\plugins\MineReset\Commands;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\SubCommand;
use ReformedDevs\Prisons_Core\Core\Utils\ServerUtils;

class HelpCommand extends SubCommand
{
	/**
	 * Checks if the CommandSender can use this command
	 * 
	 * @param CommandSender $sender
	 * 
	 * @return bool
	 * 
	 */
	public function canUse(CommandSender $sender) : bool
	{
		return true;
	}
	
	
	
	/**
	 * Executes the Help command
	 * 
	 * @param CommandSender $sender
	 * @param array $args
	 * 
	 * @return bool
	 * 
	 */
	public function execute(CommandSender $sender, array $args) : bool
	{
		if(!(isset($args[0])))
		{
			$args[0] = 1;
		}
		
		if(!(is_numeric($args[0])))
		{
			$sender->sendMessage(ServerUtils::ERROR . TF::GRAY . 'Page number must be numeric!');
			return false;
		}
		
		$pages = array_chunk(Commands::getSubCommands(), 5);
		
		$pageNumber = min(count($pages), (int)$args[0]);
		
		$list = TF::WHITE . str_repeat('-', 7) . TF::DARK_GREEN . ' Showing MineReset Commands page ' . $pageNumber . ' out of ' . count($pages) . ' ' . TF::WHITE . str_repeat('-', 7) . "\n";
		
		foreach($pages[$pageNumber - 1] as $command)
		{
			$name = $command->getName();
			$description = $command->getDescription();
			
			$list .= TF::DARK_GREEN . ucfirst($name) . TF::WHITE . ' - ' . $description . "\n";
		}
		
		$sender->sendMessage($list);
		return true;
	}
}