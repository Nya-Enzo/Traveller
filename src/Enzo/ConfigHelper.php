<?php

namespace Enzo;

use Enzo\struct\ServerInfo;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Config;

class ConfigHelper
{
    private static function getConfig(): Config
    {
        return new Config(Traveller::getInstance()->getDataFolder() . "config.yml");
    }

    private static function getItemData(): array
    {
        return self::getConfig()->get("item", []);
    }

    private static function getCommandData(): array
    {
        return self::getConfig()->get("command", []);
    }

    public static function getItemName(): string
    {
        return self::getItemData()["name"] ?? "§f- §eTraveller §f-";
    }

    public static function getItem(): Item
    {
        $item = StringToItemParser::getInstance()->parse(self::getItemData()["type"] ?? "compass");

        return $item ?? VanillaItems::AIR();
    }

    public static function isEnchanted(): bool
    {
        return (bool) self::getItemData()["enchanted"] ?? true;
    }

    public static function shouldGiveItem(bool $respawn = false): bool
    {
        return $respawn
            ? (bool) self::getItemData()["give"]["respawn"] ?? true
            : (bool) self::getItemData()["give"]["join"] ?? true;
    }

    public static function getGiveSlot(): int
    {
        return (int) self::getItemData()["give"]["slot"] ?? 4;
    }

    public static function getGiveAlias(): string
    {
        return self::getItemData()["give"]["alias"] ?? "";
    }

    public static function isCommandEnabled(): bool
    {
        return (bool) self::getCommandData()["enabled"] ?? false;
    }

    public static function getCommandName(): string
    {
        return self::getCommandData()["name"] ?? "travel";
    }

    public static function getCommandDesc(): string
    {
        return self::getCommandData()["desc"] ?? "Travel to another server without any item !";
    }

    /**
     * @return ServerInfo[]
     */
    public static function getServerInfos(): array
    {
        $list = [];

        $file = new Config(Traveller::getInstance()->getDataFolder() . "servers.json");

        foreach($file->getAll() as $data)
        {
            $list[] = ServerInfo::create($data);
        }

        return $list;
    }
}