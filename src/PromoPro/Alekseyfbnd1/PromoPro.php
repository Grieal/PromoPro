<?php

namespace PromoPro\Alekseyfbnd1;

use pocketmine\plugin\PluginBase;
use pocketmine\command\{CommandSender, Command};
use pocketmine\{Server, Player};

class PromoPro extends PluginBase {

  public $base;
    
  public function onEnable () {
    if (!is_dir($this->getDataFolder())) {
      mkdir($this->getDataFolder());
    }
    
    $this->base = new SQLite3($this, "promo");
  }
  
}
