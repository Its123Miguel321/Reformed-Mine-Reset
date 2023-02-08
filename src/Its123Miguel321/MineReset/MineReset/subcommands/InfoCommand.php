<?php

namespace ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\SubCommand;
use ReformedDevs\Prisons_Core\Core\Utils\ServerUtils;

class InfoCommand extends SubCommand
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

        $mine = $this->getManager()->getMineByName(strtolower($args[0]));

        if(is_null($mine))
        {
            $sender->sendMessage(ServerUtils::ERROR . TF::DARK_GRAY . strtolower($args[0]) . TF::GRAY . ' is not a mine!');
            return false;
        }

        $message = TF::WHITE . str_repeat('=', 5) . TF::DARK_GRAY . ' Mine ' . ucfirst($mine->getName()) . ' Info ' . TF::WHITE . str_repeat('=', 5) . "\n";
        $message .= TF::GRAY . 'Pos A: ' . TF::WHITE . $mine->getPosA() . "\n";
        $message .= TF::GRAY . 'Pos B: ' . TF::WHITE . $mine->getPosB() . "\n";
        $message .= TF::GRAY . 'Last Reset: ' . TF::WHITE . (time() - $mine->lastReset) . ' seconds ago' . "\n";
        $message .= TF::GRAY . 'Block Data:' . "\n";

        foreach($mine->getData() as $data)
        {
            $message .= str_repeat(' ', 5) . TF::GRAY . 'Block: ' . TF::DARK_PURPLE . $data['block']->getName() . "\n"; 
            $message .= str_repeat(' ', 5) . TF::GRAY . 'Chance: ' . TF::GOLD . $data['chance'] . "%\n"; 
        }

        $sender->sendMessage($message);
		return true;
	}
}