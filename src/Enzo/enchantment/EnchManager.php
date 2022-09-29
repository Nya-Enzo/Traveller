<?php

namespace Enzo\enchantment;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;

class EnchManager
{
    private static Enchantment $enchantment;

    public static function init(): void
    {
        self::$enchantment = new Enchantment("", Rarity::COMMON, ItemFlags::ALL, ItemFlags::NONE, 1);
        EnchantmentIdMap::getInstance()->register(-10, self::$enchantment);
    }

    public static function enchantItem(Item &$item): void
    {
        $item->addEnchantment(new EnchantmentInstance(self::$enchantment));
    }
}