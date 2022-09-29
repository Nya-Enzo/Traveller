<?php

namespace Enzo\command;

use Enzo\ConfigHelper;
use Enzo\TravellerForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;

class FormCommand extends Command
{
    public function __construct()
    {
        parent::__construct(ConfigHelper::getCommandName(), ConfigHelper::getCommandDesc());
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player)
        {
            $sender->sendMessage("§cPlease execute this in game.");
            return;
        }

        $sender->sendForm(new TravellerForm());
    }
}