<?php

namespace Its123Miguel321\MineReset\manager;

use pocketmine\command\CommandSender;
use pocketmine\item\StringToItemParser;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\world\Position;
use pocketmine\world\World;

use Its123Miguel321\MineReset\Mine;
use Its123Miguel321\MineReset\MineReset;
use Its123Miguel321\MineReset\ResetSettings;

class MineManager
{
    /** @var self $instance */
    public static $instance;
    /** @var MineReset $plugin */
    private $plugin;
    /** @var Config $config */
    public $config;
    /** @var Mine[] $mines */
    public $mines = [];
    /** @var array selections */
    public $selections = [];



    /**
     * Mine manager constructor
     * 
     * @param MineReset $plugin
     * 
     */
    public function __construct(MineReset $plugin)
    {
        $this->plugin = $plugin;

        self::$instance = $this;
        
        @mkdir($plugin->getDataFolder() . 'MineReset');

        $this->config = new Config($plugin->getDataFolder() . 'MineReset/mines.json', Config::JSON, array('mines' => []));

        $this->open();
    }



    /**
     * Returns the itself
     * 
     * @return self
     * 
     */
    public static function getInstance() : self
    {
        return self::$instance;
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
     * Returns the config
     * 
     * @return Config
     * 
     */
    public function getConfig() : Config
    {
        return $this->config;
    }



    /**
     * Opens the data
     * 
     */
    private function open() : void
    {
        foreach($this->getConfig()->get('mines', []) as $name => $data)
        {
            $blocks = [];

            foreach($data['data'] as $d)
            {
                $blocks[] = [
                    'block' => StringToItemParser::getInstance()->parse($d['block'])->getBlock(),
                    'chance' => $d['chance']
                ];
            }

            $mine = new Mine(
                $name, 
                new Vector3($data['posA'][0], $data['posA'][1], $data['posA'][2]), 
                new Vector3($data['posB'][0], $data['posB'][1], $data['posB'][2]), 
                Server::getInstance()->getWorldManager()->getWorldByName($data['world']),
                $blocks
            );

            $mine->reset(false, true, null, false);

            $this->mines[$name] = $mine;
        }
    }



    /**
     * Closes/Saves the data
     * 
     */
    public function save() : void
    {
        $mines = [];

        foreach($this->mines as $mine)
        {
            $data = [];

            foreach($mine->getData() as $set)
            {
                $data[] = [
                    'block' => $set['block']->getName(),
                    'chance' => $set['chance']
                ];
            }

            $mines[$mine->getName()] = [
                'posA' => [
                    $mine->getPosA()->getX(),
                    $mine->getPosA()->getY(),
                    $mine->getPosA()->getZ()
                ],
                'posB' => [
                    $mine->getPosB()->getX(),
                    $mine->getPosB()->getY(),
                    $mine->getPosB()->getZ()
                ],
                'world' => $mine->getWorld()->getFolderName(),
                'data' => $data
            ];
        }

        $this->getConfig()->set('mines', $mines);
        $this->getConfig()->save();
    }



    /**
     * Checks if the name was already given to a mine
     * 
     * @param string $name
     * 
     * @return bool
     * 
     */
    public function mineExists(string $name) : bool
    {
        return isset($this->mines[$name]);
    }



    /**
     * Creates a new mine
     * 
     * @param string $name
     * @param Position $posA
     * @param Position $posB
     * @param World $world
     * @param array $data
     * @param int $interval
     * @param ?CommandSender $sender
     * 
     * @return bool
     * 
     */
    public function createMine(string $name, Position $posA, Position $posB, World $world, array $data = [], ?CommandSender $sender = null) : bool
    {
        if($this->mineExists($name))
        {
            if($sender === null) return false;

            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'A mine with the name ' . TF::GOLD . $name . TF::GRAY . ' already exists!');
            return false;
        }

        if($posA->getWorld()->getFolderName() !== $posB->getWorld()->getFolderName())
        {
            if($sender === null) return false;

            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'The mine reset positions must be in the same world!');
            return false;
        }

        $this->mines[$name] = new Mine($name, $posA, $posB, $world, $data);

        if($sender !== null) $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'You created a new mine reset named ' . TF::WHITE . $name . TF::GRAY . '!');
        return true;
    }



    /**
     * Deletes a mine
     * 
     * @param string $name
     * @param ?CommandSender $sender
     * 
     * @return bool
     * 
     */
    public function deleteMine(string $name, ?CommandSender $sender = null) : bool
    {
        if(!($this->mineExists($name)))
        {
            if($sender === null) return false;

            $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'A mine with the name ' . TF::GOLD . $name . TF::GRAY . ' does not exist!');
            return false;
        }

        $mine = $this->getMineByName($name);
        $mine->reset(true, true, $sender);

        unset($this->mines[$name]);
        
        if($sender !== null) $sender->sendMessage(ResetSettings::PREFIX . TF::GRAY . 'You deleted a mine reset named ' . TF::WHITE . $name . TF::GRAY . '!');
        return true;
    }



    /**
     * Returns a mine by name
     * 
     * @param string $name
     * 
     * @return ?Mine
     * 
     */
    public function getMineByName(string $name) : ?Mine
    {
        return $this->mines[$name] ?? null;
    }



    /**
     * Returns all mines
     * 
     * @return Mine[]
     * 
     */
    public function getMines() : array
    {
        return $this->mines;
    }



    /**
     * Reset all mines
     * 
     * @return bool
     * 
     */
    public function resetAll() : void
    {
        foreach($this->getMines() as $mine)
        {
            $mine->reset(false, true);
        }
    }



	/**
	 * Gets a player's selection
	 * 
	 * @param $player
	 * @param int $selection
	 * 
	 * @return Position
	 * 
	 */
	public function getSelection($player, int $selection = 0) : Position
	{
		if($player instanceof Player) $player = $player->getName();
		if(!(isset($this->selections[$player]))) return [];

		return $this->selections[$player][$selection];
	}



	/**
	 * Sets a player's selection
	 * 
	 * @param $player
	 * @param int $selection
	 * @param Position $pos
	 * 
	 */
	public function setSelection($player, int $selection, Position $pos): void
	{
		if($player instanceof Player) $player = $player->getName();

		$this->selections[$player][$selection] = $pos;
	}



	/**
	 * Checks if a player has a selection
	 * 
	 * @param $player
	 * @param int $selection
	 * 
	 * @return bool
	 * 
	 */
	public function hasSelection($player, int $selection): bool
	{
		if($player instanceof Player) $player = $player->getName();
		if(!(isset($this->selections[$player]))) return false;

		return isset($this->selections[$player][$selection]);
	}
}
