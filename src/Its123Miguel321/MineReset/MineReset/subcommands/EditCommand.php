<?php

namespace ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\ResetSettings;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\SubCommand;
use ReformedDevs\Prisons_Core\Core\plugins\SellSystem\settings\SellSettings;
use ReformedDevs\Prisons_Core\Core\Utils\ServerUtils;

class EditCommand extends SubCommand
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
		return ($sender instanceof Player);
	}
	
	
	
	/**
	 * Executes the Create command
	 * 
	 * @param CommandSender $sender
	 * @param array $args
	 * 
	 * @return bool
	 * 
	 */
	public function execute(CommandSender $sender, array $args) : bool
	{
        if(!(isset($args[0]) || !(isset($args[1]))))
        {
            $sender->sendMessage(TF::BOLD . TF::RED . 'Usage: ' . TF::RESET . $this->getDescription());
            return false;
        }

        $mine = $this->getManager()->getMineByName($args[0]);

        if(is_null($mine))
        {
            $sender->sendMessage(ServerUtils::ERROR . TF::DARK_GRAY . $args[0] . TF::GRAY . ' is not a mine!');
            return false;
        }

        $orignal = $mine->getName();

        $mine->setName($args[1]);

        $sender->sendMessage(ServerUtils::SUCCESS . TF::GRAY . 'You changed mine ' . TF::DARK_GRAY . $orignal . TF::GRAY . ' to ' . TF::GOLD . $mine->getName() . TF::GRAY . '!');
		return true;
	}
}