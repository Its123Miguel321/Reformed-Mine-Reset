<?php

namespace Its123Miguel321\MineReset\tasks;

use pocketmine\block\BlockFactory;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

use Its123Miguel321\MineReset\manager\MineManager;
use Its123Miguel321\MineReset\MineReset;
use Its123Miguel321\MineReset\ResetSettings;

class AsyncResetTask extends AsyncTask
{
    /** @var string $name */
    private $name;
    /** @var string $data */
    private $data;
    /** @var Vector3 $posA */
    private $posA;
    /** @var Vector3 $posB */
    private $posB;
    /** @var string $world */
    private $world;
    /** @var bool $clear */
    private $clear;
    /** @var bool $announce */
    private $announce;



    /**
     * The reset task constructor
     * 
     * @param bool $clear
     * 
     */
    public function __construct(string $name, array $data, Vector3 $posA, Vector3 $posB, string $worldName, bool $clear = false, bool $announce = false)
    {
        $this->name = $name;
        $this->data = serialize($data);
        $this->posA = serialize($posA);
        $this->posB = serialize($posB);
        $this->world = $worldName;
        $this->clear = $clear;
        $this->announce = $announce;
    }



    /**
     * what happens on run
     * 
     */
    public function onRun(): void
    {
        $data = [];

        $posA = unserialize($this->posA);
        $posB = unserialize($this->posB);

        $minX = min($posA->getX(), $posB->getX());
		$minY = min($posA->getY(), $posB->getY());
		$minZ = min($posA->getZ(), $posB->getZ());
		$maxX = max($posA->getX(), $posB->getX());
		$maxY = max($posA->getY(), $posB->getY());
		$maxZ = max($posA->getZ(), $posB->getZ());
        for($x = $minX; $x <= $maxX; $x++)
        {
			for($z = $minZ; $z <= $maxZ; $z++)
            {
				for($y = $minY; $y <= $maxY; $y++)
                {
					$chance = mt_rand(0, 100);
                    $isFound = false;
                    $block = null;

                    if($this->clear)
                    {
                        $block = 0;
                    }else{
                        while($isFound === false)
                        {
                            $random = unserialize($this->data)[array_rand(unserialize($this->data))];

                            if($random['chance'] >= 100 - $chance)
                            {
                                $block = $random['block'];
                                $isFound = true;
                            }

                            $chance = mt_rand(0, 100);
                        }
                    }

                    $data[] = [
                        $x,
                        $z,
                        $y,
                        $block
                    ];
				}
			}
		}

        $this->setResult($data);
    }



    /**
     * What happens when task is complete
     * 
     */
    public function onCompletion() : void
    {
        $results = $this->getResult();
        $server = Server::getInstance();
        /** @var MineReset $plugin */
        $plugin = $main->getEnabledPlugins()->getPlugin('MineReset');
        /** @var MineManager $manager */
        $manager = $plugin->getManager();
        $world = $server->getWorldManager()->getWorldByName($this->world);

        $posA = unserialize($this->posA);
        $posB = unserialize($this->posB);

        $minX = min($posA->getX(), $posB->getX());
		$minY = min($posA->getY(), $posB->getY());
		$minZ = min($posA->getZ(), $posB->getZ());
		$maxX = max($posA->getX(), $posB->getX());
		$maxY = max($posA->getY(), $posB->getY());
		$maxZ = max($posA->getZ(), $posB->getZ());
		$bb = new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);

        foreach($world->getCollidingEntities($bb) as $entity)
        {
			if(!($entity instanceof Player)) continue;

            Server::getInstance()->dispatchCommand($entity, 'mine ' . $this->name);
		}

        foreach($results as $data)
        {
            $arr = explode(':', $data[3]);
            $block = BlockFactory::getInstance()->get($arr[0], $arr[1] ?? 0);

            $world->setBlockAt($data[0], $data[2], $data[1], $block, false);
        }

        if($this->announce) Server::getInstance()->broadcastMessage(ResetSettings::PREFIX . TF::DARK_GRAY . 'Mine ' . ucfirst($this->name) . TF::GRAY . ' has just been reset!');

        $manager->getMineByName($this->name)->lastReset = time();
        $manager->getMineByName($this->name)->isResetting = false;
    }
}
