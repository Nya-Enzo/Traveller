<?php

namespace Enzo;

use Enzo\command\FormCommand;
use Enzo\enchantment\EnchManager;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use Ramsey\Uuid\Uuid;

class Traveller extends PluginBase implements Listener
{
    use SingletonTrait;

    protected function onEnable(): void
    {
        self::setInstance($this);
        $this->saveConfig();
        $this->saveResource("servers.json");
        EnchManager::init();
        $this->getLogger()->info("§ePlugin enabled !");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        {
            $alias = ConfigHelper::getGiveAlias();

            if(trim($alias) !== "")
                StringToItemParser::getInstance()->register($alias, fn() => self::makeItem());
        }

        {
            if(ConfigHelper::isCommandEnabled())
                Server::getInstance()->getCommandMap()->register("Traveller", new FormCommand());
        }
    }

    public static function makeItem(): ?Item
    {
        $item = ConfigHelper::getItem();
        if($item->isNull())
            return null;

        $item->setCount(1);
        $item->setCustomName("§r" . ConfigHelper::getItemName());
        if(ConfigHelper::isEnchanted())
            EnchManager::enchantItem($item);

        $item->getNamedTag()->setString("Traveller", Uuid::uuid4()->toString());
        return $item;
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        if(ConfigHelper::shouldGiveItem())
        {
            $item = self::makeItem();
            if(is_null($item))
                return;

            $event->getPlayer()->getInventory()->setItem(ConfigHelper::getGiveSlot(), $item);
        }
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        if(ConfigHelper::shouldGiveItem(true))
        {
            $item = self::makeItem();
            if(is_null($item))
                return;

            $event->getPlayer()->getInventory()->setItem(ConfigHelper::getGiveSlot(), $item);
        }
    }

    public function onPreDeath(EntityDamageByEntityEvent $event)
    {
        $victim = $event->getEntity();

        if($victim instanceof Player)
        {
            if($event->getFinalDamage() >= $victim->getHealth())
            {
                foreach($victim->getInventory()->getContents() as $slot => $item)
                {
                    if($item->getNamedTag()->getTag("Traveller") !== null)
                        $victim->getInventory()->setItem($slot, VanillaItems::AIR());
                    //Don't drop the traveller on death
                }
            }
        }
    }

    public function onItemUse(PlayerItemUseEvent $event)
    {
        $item = $event->getItem();

        if($item->getNamedTag()->getTag("Traveller") !== null)
            $event->getPlayer()->sendForm(new TravellerForm());
    }
}