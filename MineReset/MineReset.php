<?php
namespace ReformedDevs\Prisons_Core\Core\plugins\MineReset;

use ReformedDevs\Prisons_Core\Core\Utils\CorePlugin;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\manager\MineManager;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\tasks\AutoResetTask;

class MineReset extends CorePlugin
{
    /**
     * What happens when CorePlugin is enabled!
     * 
     */
    public function onEnable() : void
    {
        $this->manager = new MineManager($this);

        $this->getCommandMap()->register($this->getName(), new Commands($this));
        $this->getPluginManager()->registerEvents(new EventListener($this), $this->getMain());

        $this->getMain()->getScheduler()->scheduleRepeatingTask(new AutoResetTask($this), 20 * ResetSettings::RESET_INTERVAL);
    }
    
    
    
    /**
     * What happens when CorePlugin is disabled!
     * 
     */
    public function onDisable() : void
    {
        $this->getManager()->save();
    }
}
