<?php

namespace Enzo\struct;

use pocketmine\Server;

class ServerInfo
{
    public function __construct(
        public string $displayName,
        public string $ip,
        public int $port,
        public string $formIconLink,
        public string $formIconType,
        public bool $showAddressToForm
    )
    {
        if($this->ip === "current")
            $this->ip = ($currIp = Server::getInstance()->getIp()) === "0.0.0.0" ? "127.0.0.1" : $currIp;
        if($this->port === -1)
            $this->port = Server::getInstance()->getPort();
        $this->formIconType = strtolower($this->formIconType);
        if(!in_array($this->formIconType, ["url", "path", "none"], true))
            $this->formIconType = "none";
    }

    public static function create(array $data): self
    {
        $display = $data["display-name"] ?? "Unknown Server";
        $connectInfo = $data["info"] ?? ["ip" => "current", "port" => -1];

        $ip = $connectInfo["ip"];
        $port = $connectInfo["port"];

        $icon = $data["icon"] ?? "";
        $iconType = $data["icon-type"] ?? "none";

        $showAddress = (bool) $data["show-address-to-form"] ?? false;

        return new self($display, $ip, $port, $icon, $iconType, $showAddress);
    }
}