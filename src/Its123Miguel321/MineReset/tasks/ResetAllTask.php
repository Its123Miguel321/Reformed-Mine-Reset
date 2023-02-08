<?php

namespace Its123Miguel321\MineReset\tasks;

use pocketmine\command\CommandSender;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use Its123Miguel321\MineReset\Mine;
use Its123Miguel321\MineReset\ResetSettings;

class ResetAllTask extends Task
{
    /** @var Mine[] $mines */
    private $mines;
    /** @var ?CommandSender $sender */
    private $sender;
    /** @var int $count */
    private $count;
    /** @var int $start */
    private $start;



    /**
     * The reset task constructor
     * 
     * @param Mine[] $mines
     * 
     */
    public function __construct(array $mines, ?CommandSender $sender = null, int $start)
    {
        $this->mines = array_values($mines);
        $this->sender = $sender;
        $this->count = count($this->mines) + 1;
        $this->start = $start;
    }



    /**
     * what happens on run
     * 
     */
    public function onRun(): void
    {
        $mine = $this->mines[0] ?? null;

        if($mine !== null)
        {
            $mine->reset(false, true, null, false);

            unset($this->mines[0]);

            $mines = [];

            foreach($this->mines as $mine)
            {
                $mines[] = $mine;
            }

            $this->mines = $mines;
        }

        if(empty($this->mines) && $this->count === 0)
        {
            $time = round(microtime(true) - $this->start, 2);

            Server::getInstance()->broadcastMessage(ResetSettings::PREFIX . TF::GRAY . 'All mines have been reset!');
            $this->sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'You reset all mines! (' . $time . ' seconds)');
            $this->getHandler()->cancel();
        }else{
            $this->count--;
        }
    }
}
