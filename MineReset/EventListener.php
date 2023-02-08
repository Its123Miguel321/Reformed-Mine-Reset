<?php

namespace ReformedDevs\Prisons_Core\Core\plugins\MineReset;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat as TF;

class EventListener implements Listener
{
    /** @var MineReset $plugin */
    private $plugin;
    /** @var array $wandClicks */
    protected $wandClicks = [];



    /**
     * EventListener for MineReset constructor
     * 
     * @param MineReset $plugin
     * 
     */
    public function __construct(MineReset $plugin)
    {
        $this->plugin = $plugin;
    }



    /**
     * Returns the plugin
     * 
     * @return MineReset
     * 
     */
    public function getPlugin() : MineReset
    {
        return $this->plugin;
    }



	/**
	 * Selects the first position
	 * 
	 * @param BlockBreakEvent $event
     * 
	 */
	public function onPos1(BlockBreakEvent $event): void
	{
        if($event->isCancelled()) return;

		$player = $event->getPlayer();
		$block = $event->getBlock();
		$item = $event->getItem();

        if(!($item->equals(VanillaItems::STICK(), true, false) && $item->getNamedTag()->getByte('isWand', false))) return;

		$event->cancel();

		$x = $block->getPosition()->getX();
		$y = $block->getPosition()->getY();
		$z = $block->getPosition()->getZ();
		$world = $block->getPosition()->getWorld()->getFolderName();
        $position = implode(TF::GRAY . ', ' . TF::GOLD, [$x, $y, $z]);

		$this->getPlugin()->getManager()->setSelection($player, ResetSettings::FIRST_SELECTION, $block->getPosition());

		$player->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'Selected first position at ' . TF::GOLD . $position . TF::GRAY . ' in ' . TF::GOLD . $world . TF::GRAY . '!');
	}



	/**
	 * Selects the second position
	 * 
	 * @param PlayerInteractEvent $event
	 * 
	 */
	public function onPos2(PlayerInteractEvent $event): void
	{
		if($event->isCancelled()) return;

		$player = $event->getPlayer();
		$block = $event->getBlock();
		$item = $event->getItem();

		if(!($item->equals(VanillaItems::STICK(), true, false) && $item->getNamedTag()->getByte('isWand', false))) return;
		if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) return;
		if(isset($this->wandClicks[$player->getName()]) && microtime(true) - $this->wandClicks[$player->getName()] < 0.5) return;
		
        $event->cancel();

		$this->wandClicks[$player->getName()] = microtime(true);

		$x = $block->getPosition()->getX();
		$y = $block->getPosition()->getY();
		$z = $block->getPosition()->getZ();
		$world = $block->getPosition()->getWorld()->getDisplayName();
        $position = implode(TF::GRAY . ', ' . TF::GOLD, [$x, $y, $z]);

		$this->getPlugin()->getManager()->setSelection($player, ResetSettings::SECOND_SELECTION, $block->getPosition());

		$player->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'Selected second position at ' . TF::GOLD . $position . TF::GRAY . ' in ' . TF::GOLD . $world . TF::GRAY . '!');
	}
}