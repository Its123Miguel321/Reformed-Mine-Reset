<?php

namespace ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands;

use pocketmine\command\CommandSender;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\manager\MineManager;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\MineReset;

abstract class SubCommand
{
	/** @var MineReset $plugin */
	public $plugin;
	/** @var string $name */
	public $name;
	/** @var string $description */
	public $description;
	/** @var string $alias */
	public $alias;
	
	
	
	/**
	 * SubCommand constructor
	 * 
	 * @param MineReset $plugin
	 * @param string $name
	 * @param string $description
	 * 
	 */
	public function __construct(MineReset $plugin, string $name, string $description = "", string $alias = "")
	{
		$this->plugin = $plugin;
		$this->name = $name;
		$this->description = $description;
		$this->alias = $alias;
	}
	
	
	
	/**
	 * Returns the plugin
	 * 
	 * @return MineReset
	 * 
	 */
	final public function getPlugin() : MineReset
	{
		return $this->plugin;
	}
	
	
	
	/**
	 * Returns the api
	 * 
	 * @return MineManager
	 * 
	 */
	final public function getManager() : MineManager
	{
		return MineManager::getInstance();
	}
	
	
	
	/**
	 * Returns SubCommand name
	 * 
	 * @return string
	 * 
	 */
	final public function getName() : string
	{
		return $this->name;
	}
	
	
	
	/**
	 * Returns SubCommand description
	 * 
	 * @return string
	 * 
	 */
	final public function getDescription() : string
	{
		return $this->description;
	}
	
	
	
	/**
	 * Returns SubCommand description
	 * 
	 * @return string
	 * 
	 */
	final public function getAlias() : string
	{
		return $this->alias;
	}

	
	
	
	abstract public function canUse(CommandSender $sender) : bool;
	
	abstract public function execute(CommandSender $sender, array $args) : bool;
}