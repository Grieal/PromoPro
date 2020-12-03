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
  
  public function onCommand (CommandSender $sender, Command $cmd, string $label, array $args) :bool {
    switch ($cmd->getName()) {
      case "promo":
        if (isset($args[0])) {
          if ($args[0] == "add") {
            if (isset($args[1]) && isset($args[2]) && isset($args[3])) {
              if (ctype_digit($args[1]) && ctype_digit($args[2]) && ctype_digit($args[3])) {
                
              }
              else {
                $sender->sendMessage("Время/кол-во/приз должно быть числом!");
              }
            }
            else {
              $sender->sendMessage("/promo add [время] [кол-во] [приз]");
            }
          }
          if ($args[0] == "remove") {
            
          }
          if ($args[0] == "get") {
            
          }
        }
        else {
          $sender->sendMessage("/promo [add|remove|get]");
        }
      break;
    }
  }
  
}
