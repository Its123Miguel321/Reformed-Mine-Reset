<?php

namespace ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\ResetSettings;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\SubCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\tasks\ResetAllTask;

class ResetAllCommand extends SubCommand
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
	 * Executes the Reset All command
	 * 
	 * @param CommandSender $sender
	 * @param array $args
	 * 
	 * @return bool
	 * 
	 */
	public function execute(CommandSender $sender, array $args) : bool
	{
		$sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'Resetting all mines, this might take a while...');

        $this->getPlugin()->getMain()->getScheduler()->scheduleRepeatingTask(new ResetAllTask($this->getManager()->getMines(), $sender, microtime(true)), 10 * 3);
		
		return true;
	}
}