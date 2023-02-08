<?php

namespace ReformedDevs\Prisons_Core\Core\plugins\MineReset;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\SubCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\MineReset;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\CreateCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\DeleteCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\EditCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\HelpCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\InfoCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\ListCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\MineThemeCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\ResetAllCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\ResetCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\SetCommand;
use ReformedDevs\Prisons_Core\Core\plugins\MineReset\subcommands\WandCommand;

class Commands extends Command
{
	/** @var MineReset $plugin */
	public $plugin;
	/** @var array $subCommands */
	public static $subCommands = [];
	/** @var array $aliasSubCommands */
	public static $aliasSubCommands = [];
	
	
	
	/**
	 * Commands constructor
	 * 
	 * @param MineReset $plugin
	 * 
	 */
	public function __construct(MineReset $plugin)
	{
		$this->plugin = $plugin;
		
		parent::__construct('minereset');
		$this->setDescription('MineReset commands');
		$this->setUsage('§cUnknown command. Try §f/minereset help §cfor a list of all commands!');
		$this->setAliases(['reset']);
		$this->setPermission('minereset.commands');
		
		self::loadSubCommand(new CreateCommand($this->getPlugin(), 'create', '/minereset create <string: name> <block_name:chance block_name:chance etc...>', 'make'));
		self::loadSubCommand(new DeleteCommand($this->getPlugin(), 'delete', '/minereset delete <string: name>', 'destroy'));
		self::loadSubCommand(new EditCommand($this->getPlugin(), 'edit', '/minereset edit <string: mine> <string: new_name>'));
		self::loadSubCommand(new HelpCommand($this->getPlugin(), 'help', '/minereset help [int: page]', '?'));
		self::loadSubCommand(new InfoCommand($this->getPlugin(), 'info', '/minereset info <string: name>'));
		self::loadSubCommand(new ListCommand($this->getPlugin(), 'list', '/minereset list [int: page]'));
		self::loadSubCommand(new ResetCommand($this->getPlugin(), 'reset', '/minereset reset <string: name> [bool: force]'));
		self::loadSubCommand(new ResetAllCommand($this->getPlugin(), 'resetall', '/minereset resetall'));
		self::loadSubCommand(new SetCommand($this->getPlugin(), 'set', '/minereset set <string: name> <block_name:chance block_name:chance etc...>'));
		self::loadSubCommand(new WandCommand($this->getPlugin(), 'wand', '/minereset wand'));
	}
	
	
	
	/**
	 * Loads subcommand
	 * 
	 * @param SubCommand $command
	 * 
	 */
	public static function loadSubCommand(SubCommand $command) : void
	{
		self::$subCommands[$command->getName()] = $command;
		
		if($command->getAlias() == '') return;
		
		self::$aliasSubCommands[$command->getAlias()] = $command;
		
		
	}
	
	
	
	/**
	 * Unloads subcommand
	 * 
	 * @param $command
	 * 
	 */
	public static function unloadSubCommand($command) : void
	{
		$command = self::getSubCommand($command);
		
		if($command === null) return;
		
		unset(self::$subCommands[$command->getName()]);
		unset(self::$aliasSubCommands[$command->getAlias()]);
	}
	
	
	
	/**
	 * Gets a subcommand
	 * 
	 * @param string $command
	 * 
	 * @return SubCommand|null
	 * 
	 */
	public static function getSubCommand(string $command) : SubCommand|null
	{
		$command = self::$subCommands[$command] ?? self::$aliasSubCommands[$command] ?? null;
		
		return $command;
	}
	
	
	
	/**
	 * Gets all subcommands
	 * 
	 * @return array
	 * 
	 */
	public static function getSubCommands() : array
	{
		return self::$subCommands;
	}
	
	
	
	/**
	 * Gets all alias subcommands
	 * 
	 * @return array
	 * 
	 */
	public static function getAliasSubCommands() : array
	{
		return self::$aliasSubCommands;
	}
	
	
	
	/**
	 * Executes command
	 * 
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 * 
	 * @return bool
	 * 
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool
	{
		if(!(isset($args[0])))
		{
			$args[0] = "help";
		}

		$command = strtolower((string)array_shift($args));

		if(isset(self::$subCommands[$command]))
		{
			$command = self::$subCommands[$command];
		}elseif(isset(self::$aliasSubCommands[$command]))
		{
			$command = self::$aliasSubCommands[$command];
		}else
		{
			$sender->sendMessage($this->getUsage());
			return false;
		}

		if(!($command->canUse($sender)) || !($sender->hasPermission($this->getPermission())))
		{
			$sender->sendMessage("§l§c(!) §r§7You do not have permission to use this command!");
			return false;
		}

		return $command->execute($sender, $args);
	}
	
	
	
	/**
	 * Returns MineReset
	 * 
	 * @return MineReset
	 * 
	 */
	public function getPlugin() : MineReset
	{
		return $this->plugin;
	}
}