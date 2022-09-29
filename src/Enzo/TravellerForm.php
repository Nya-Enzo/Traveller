<?php

namespace Enzo;

use EasyUI\element\Button;
use EasyUI\icon\ButtonIcon;
use EasyUI\variant\SimpleForm;
use pocketmine\player\Player;

class TravellerForm extends SimpleForm
{
    public function __construct()
    {
        parent::__construct(ConfigHelper::getItemName());
    }

    protected function onCreation(): void
    {
        $informations = ConfigHelper::getServerInfos();

        foreach($informations as $info)
        {
            $tip = ($info->showAddressToForm ? "\n§7$info->ip:$info->port" : "");
            $button = new Button($info->displayName . $tip);
            if($info->formIconType !== "none")
                $button->setIcon(new ButtonIcon($info->formIconLink, $info->formIconType));

            $button->setSubmitListener(function (Player $player) use($info)
            {
                $player->transfer($info->ip, $info->port);
            });

            $this->addButton($button);
        }
    }
}