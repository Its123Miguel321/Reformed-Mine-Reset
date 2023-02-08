<?php

namespace ReformedDevs\Prisons_Core\Core\plugins\MineReset\tasks;

use pocketmine\block\VanillaBlocks;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use pocketmine\world\format\Chunk;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\Mine;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\MineReset;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\ResetSettings;

class AutoResetTask extends Task
{
    /** @var MineReset $plugin */
    private $plugin;



    /**
     * The reset task constructor
     * 
     * @param MineReset $plugin
     * 
     */
    public function __construct(MineReset $plugin)
    {
        $this->plugin = $plugin;
    }



    /**
     * Returns the mine
     * 
     * @return MineReset
     * 
     */
    public function getPlugin() : MineReset
    {
        return $this->plugin;
    }



    /**
     * what happens on run
     * 
     */
    public function onRun(): void
    {
        /** @var Mine $mine */
        foreach($this->getPlugin()->getManager()->getMines() as $mine)
        {
            $minX = min($mine->getPosA()->getX(), $mine->getPosB()->getX());
            $maxX = max($mine->getPosA()->getX(), $mine->getPosB()->getX());
            $minZ = min($mine->getPosA()->getZ(), $mine->getPosB()->getZ());
            $maxZ = max($mine->getPosA()->getZ(), $mine->getPosB()->getZ());
            $minY = min($mine->getPosA()->getY(), $mine->getPosB()->getY());
            $maxY = max($mine->getPosA()->getY(), $mine->getPosB()->getY());

            $empty = 0;
            $total = 0;

            for($x = $minX; $x <= $maxX; $x++)
            {
                for($z = $minZ; $z <= $maxZ; $z++)
                {
                    for($y = $minY; $y <= $maxY; $y++)
                    {
                        $total++;

                        if(!($mine->getWorld()->isLoaded())) break 3;

                        $block = $mine->getWorld()->getBlockAt($x, $y, $z, false, false);
                        $chunkX = $x >> Chunk::COORD_BIT_SIZE;
		                $chunkZ = $z >> Chunk::COORD_BIT_SIZE;

                        if($block->isSameType(VanillaBlocks::AIR()) && $mine->getWorld()->isChunkLoaded($chunkX, $chunkZ)) $empty++;
                    }
                }
            }

            if($empty >= $total / 2)
            {
                $mine->reset(false, true);

                Server::getInstance()->broadcastMessage(ResetSettings::PREFIX . TF::DARK_GRAY . $mine->getName() . TF::GRAY . ' has just been reset!');
            }
        }
    }
}