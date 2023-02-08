<?php

namespace ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\ResetSettings;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\SubCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\tasks\ResetTask;

class ResetCommand extends SubCommand
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
	 * Executes the Delete command
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
            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'You must enter a name for the mine you want to reset');
            return false;
        }

        $name = trim(TF::clean($args[0]), ' ');
        $mine = $this->getManager()->getMineByName($name);

        if($mine === null)
        {
            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'A mine with the name ' . TF::GOLD . $name . TF::GRAY . ' does not exist!');
			return false;
        }

        $force = false;
		$isAsync = true;

        if(isset($args[1])) $force = $args[1];
		if(isset($args[2])) $isAsync = $args[2];
		
		return $mine->reset(false, $force, $sender, $isAsync);
	}
}