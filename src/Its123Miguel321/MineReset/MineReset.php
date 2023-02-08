<?php

namespace Its123Miguel321\MineReset;

use Its123Miguel321\MineReset\manager\MineManager;
use Its123Miguel321\MineReset\tasks\AutoResetTask;

class MineReset extends PluginBase
{
    /** @var MineManager $manager */
    private $manager;
    
    
    
    /**
     * What happens when PluginBase is enabled!
     * 
     */
    public function onEnable() : void
    {
        $this->manager = new MineManager($this);

        $this->getCommandMap()->register($this->getName(), new Commands($this));
        $this->getPluginManager()->registerEvents(new EventListener($this), $this->getMain());

        $this->getScheduler()->scheduleRepeatingTask(new AutoResetTask($this), 20 * ResetSettings::RESET_INTERVAL);
    }
    
    
    
    /**
     * What happens when CorePlugin is disabled!
     * 
     */
    public function onDisable() : void
    {
        $this->manager->save();
    }
}
