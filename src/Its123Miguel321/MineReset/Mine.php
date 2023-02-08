<?php

namespace Its123Miguel321\MineReset;

use pocketmine\block\Block;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use pocketmine\world\World;
use Its123Miguel321\MineReset\tasks\AsyncResetTask;
use Its123Miguel321\MineReset\tasks\ResetTask;

class Mine
{
    /** @var string $name */
    public $name;
    /** @var Vector3 $posA */
    protected $posA;
    /** @var Vector3 $posB */
    protected $posB;
    /** @var World $world */
    protected $world;
    /** @var array $data */
    protected $data;
    /** @var int $lastReset */
    public $lastReset;
    /** @var bool $isResetting */
    public $isResetting = false;


    /**
     * Mine constructor
     * 
     * @param string $name
     * @param Vector3 $posA
     * @param Vector3 $posB
     * @param World $world
     * @param array $data
     * 
     */
    public function __construct(string $name, Vector3 $posA, Vector3 $posB, World $world, array $data = [])
    {
        $this->name = $name;
        $this->posA = $posA;
        $this->posB = $posB;
        $this->world = $world;
        $this->data = $data;

        $this->lastReset = time() - 300;
    }



    /**
     * Changes the name of the mine
     * 
     * @param string $name
     * 
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }



    /**
     * Returns the name of the mine
     * 
     * @return string
     * 
     */
    public function getName() : string
    {
        return $this->name;
    }



    /**
     * Returns position A
     * 
     * @return Vector3
     * 
     */
    public function getPosA() : Vector3
    {
        return $this->posA;
    }



    /**
     * Returns position B
     * 
     * @return Vector3
     * 
     */
    public function getPosB() : Vector3
    {
        return $this->posB;
    }



    /**
     * Returns the world
     * 
     * @return World
     * 
     */
    public function getWorld() : World
    {
        return $this->world;
    }



    /**
     * Returns the mine data
     * 
     * @return array
     * 
     */
    public function getData() : array
    {
        return $this->data;
    }



    /**
     * Sets the mine data
     * 
     * @param array $data
     * 
     */
    public function setData(array $data) :void
    {
        $this->data = $data;
    }



    /**
     * Returns the last time the mine was reset
     * 
     * @return int
     * 
     */
    public function getLastReset() : int
    {
        return $this->lastReset;
    }



    /**
     * Checks if the mine is resetting
     * 
     * @return bool
     * 
     */
    public function isResetting() : bool
    {
        return $this->isResetting;
    }



    /**
     * Resets the mine
     * 
     * @param ?CommandSender $sender
     * @param bool $force
     * 
     * @return bool
     * 
     */
    public function reset(bool $clear = false, bool $force = false, ?CommandSender $sender = null, bool $announce = true) : bool
    {
        if($this->isResetting() && !($force))
        {
            if($sender === null) return false;

            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'This mine is already resetting! Please wait!');
            return false;
        }

        if(time() - $this->getLastReset() >= ResetSettings::RESET_INTERVAL && !($force))
        {
            if($sender === null) return false;

            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'You can not reset this mine, it was just reset ' . TF::WHITE . (time() - $this->getLastReset()) . TF::GRAY . ' seconds ago!');
            return false;
        }

        $this->isResetting = true;

        $data = [];

        foreach($this->data as $d)
        {
            /** @var Block $block */
            $block = $d['block'];

            $data[] = [
                'block' => $block->getId() . ':' . $block->getMeta(),
                'chance' => $d['chance']
            ];
        }

        Server::getInstance()->getAsyncPool()->submitTask(new AsyncResetTask($this->name, $data, $this->posA->asVector3(), $this->posB->asVector3(), $this->world->getFolderName(), false, $announce));

        if(!($clear) && $sender !== null) $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'You reset mine ' . TF::WHITE . $this->getName() . TF::GRAY . '!');
        
        $this->lastReset = time();

        return true;
    }
}
