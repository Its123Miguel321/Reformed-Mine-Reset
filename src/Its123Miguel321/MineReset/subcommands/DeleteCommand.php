<?php

namespace Its123Miguel321\MineReset\subcommands;

use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

use Its123Miguel321\MineReset\ResetSettings;
use Its123Miguel321\MineReset\subcommands\SubCommand;
use Its123Miguel321\MineReset\tasks\ResetTask;

class DeleteCommand extends SubCommand
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
            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'You must enter a name for the mine you want to delete!');
            return false;
        }

        $name = trim(TF::clean($args[0]), ' ');
		$mine = $this->getManager()->getMineByName($name);

		if($mine === null)
		{
			$sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'A mine with the name ' . TF::GOLD . $name . TF::GRAY . ' does not exist!');
			return false;
		}

		return $this->getManager()->deleteMine($name, $sender);
	}
}
