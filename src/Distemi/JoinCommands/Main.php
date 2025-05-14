<?php

declare(strict_types = 1);

namespace Distemi\JoinCommands;

use pocketmine\event\Listener;
use pocketmine\lang\Language;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase implements Listener {
    use SingletonTrait;

    private bool $enabled = true;
    private array $commands = [];
    private Config $config;
    private ConsoleCommandSender $consoleSender;

    public function onEnable() : void{
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->config = $this->getConfig();

        $this->consoleSender = new ConsoleCommandSender($this->getServer(), new Language("eng"));
        $this->enabled = $this->config->get("Enabled");
        $this->commands = $this->config->get("Commands");
    }
    
    public function JoinEvent(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        if ($this->enabled) {
            foreach ($this->commands as $a) {
                $a = str_replace("%player%", $player->getName(), $a);
                $this->getServer()->dispatchCommand($this->consoleSender, $a);
            }
        }
    }
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        $enbl = $this->enabled;
        $this->reloadConfig();

        $this->config = $this->getConfig();
        $this->enabled = $this->config->get("Enabled");
        $this->commands = $this->config->get("Commands");
        $sender->sendMessage("JoinCommands config reloaded!");

        if ($enbl != $this->enabled && !$this->enabled) {
            $sender->sendMessage("JoinCommands disabled!");
        }

        return true;
    }
    
    public function addCommand(String $command): void
    {
        $this->commands[] = $command;
    }
    public function addCommands(Array $commands): void
    {
        foreach ($commands as $command) {
            $this->commands[] = $command;
        }
    }
}