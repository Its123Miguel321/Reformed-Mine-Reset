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

class SetCommand extends SubCommand
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
        if(!(isset($args[0])))
        {
            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'You must enter a name for the mine!');
            return false;
        }

        $name = trim(TF::clean($args[0]), ' ');
        $mine = $this->getManager()->getMineByName($name);

        if(is_null($mine))
        {
            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'A mine with the name ' . TF::GOLD . $name . TF::GRAY . ' does not exist!');
			return false;
        }

		if(!(isset($args[1])))
		{
			$sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'Missing mine block data!');
			$sender->sendMessage($this->getDescription());
			return false;
		}

		$data = [];
		$sets = array_slice($args, 1);
		$data = [];
		$totalPercent = 0;
			
		foreach($sets as $set)
		{
			if(!(strpos($set, ':')))
			{
				$sender->sendMessage(ResetSettings::PREFIX . TF::RED . 'Your format for the block data is wrong! (block_name:chance)');
				return false;
			}

			$set = explode(':', $set);
			$block_name = (string) str_replace('_', ' ', $set[0]);
			$chance = (int) $set[1];

            if(!(isset(SellSettings::BLOCK_PRICES[strtolower($block_name)])))
            {
                $sender->sendMessage(ResetSettings::PREFIX . TF::DARK_GRAY . $block_name . TF::RED . ' is not a sellable block! Please try a different block!');
                return false;
            }

			$totalPercent += $chance;

			$data[] = [
				'block' => StringToItemParser::getInstance()->parse($block_name)->getBlock(),
				'chance' => $chance
			];
		}

		if($totalPercent !== 100)
		{
			$sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'The percents must add up to 100! You have a total of ' . $totalPercent . '!');
			return false;
		}

        $mine->setData($data);

        $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'You have changed the block data for mine ' . TF::WHITE . $name . TF::GRAY . '!');
		return true;
	}
}