<?php

namespace ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use ReformedDevs\Prisons_Core\Core\plugins\CustomEnchantments\manager\CustomEnchantManager;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\ResetSettings;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\SubCommand;

class WandCommand extends SubCommand
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
	 * Executes the Wand command
	 * 
	 * @param CommandSender $sender
	 * @param array $args
	 * 
	 * @return bool
	 * 
	 */
	public function execute(CommandSender $sender, array $args) : bool
	{
        /** @var Player $sender */
        $item = VanillaItems::STICK()->setCustomName(ResetSettings::WAND_NAME)->setLore([
            '',
            TF::GOLD . 'Break' . TF::GRAY . ' a block to select the first position!',
            TF::GOLD . 'Click/Tap' . TF::GRAY . ' on a block to select second position!'
        ]);

		$item->addEnchantment(new EnchantmentInstance(CustomEnchantManager::TEST(), 1));
        $item->getNamedTag()->setByte('isWand', true);

        if($sender->getInventory()->canAddItem($item))
        {
            $sender->getInventory()->addItem($item);
        }else{
            $sender->getWorld()->dropItem($sender->getLocation(), $item);
        }

        $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'You were given the mine reset wand!');
		return true;
	}
}