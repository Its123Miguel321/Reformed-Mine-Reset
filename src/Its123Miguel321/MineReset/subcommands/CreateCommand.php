<?php

namespace Its123Miguel321\MineReset\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

use Its123Miguel321\MineReset\ResetSettings;
use Its123Miguel321\MineReset\subcommands\SubCommand;

class CreateCommand extends SubCommand
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
        if(!($this->getManager()->hasSelection($sender, ResetSettings::FIRST_SELECTION) && $this->getManager()->hasSelection($sender, ResetSettings::SECOND_SELECTION)))
        {
            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'You must have ' . TF::WHITE . '2' . TF::GRAY . ' positions selected!');
            return false;
        }

        $posA = $this->getManager()->getSelection($sender, ResetSettings::FIRST_SELECTION);
        $posB = $this->getManager()->getSelection($sender, ResetSettings::SECOND_SELECTION);

        if($posA->getWorld()->getFolderName() !== $posB->getWorld()->getFolderName())
        {
            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'Your selections must be in the same world!');
            return false;
        }

        if(!(isset($args[0])))
        {
            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'You must enter a name for the mine!');
            return false;
        }

		if(!(isset($args[1])))
		{
			$sender->sendMessage(TF::GRAY . 'Missing the block data for the mine!');
			return false;
		}

        $name = trim(TF::clean($args[0]), ' ');
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
		
		return $this->getManager()->createMine($name, $posA, $posB, $posA->getWorld(), $data, $sender);
	}
}
