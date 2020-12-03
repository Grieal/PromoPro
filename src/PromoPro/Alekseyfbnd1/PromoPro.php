<?php

namespace PromoPro\Alekseyfbnd1;

use pocketmine\plugin\PluginBase;
use pocketmine\command\{CommandSender, Command};
use pocketmine\{Server, Player};

class PromoPro extends PluginBase {

  public $promo;
  public $users;
    
  public function onEnable () {
    if (!is_dir($this->getDataFolder())) {
      mkdir($this->getDataFolder());
    }
    
    $this->promo = new SQLite3($this, "promo");
    $this->users = new SQLite3($this, "users");
  }
  
  public function onCommand (CommandSender $sender, Command $cmd, string $label, array $args) :bool {
    switch ($cmd->getName()) {
      case "promo":
        if (isset($args[0])) {
          if ($args[0] == "add" && $sender->isOp()) {
            if (isset($args[1]) && isset($args[2]) && isset($args[3]) && isset($args[4])) {
              if (ctype_alnum($args[1])) {
                if (ctype_digit($args[2]) || ctype_digit($args[3]) || ctype_digit($args[4])) {
                  $this->promo->prepare("SELECT * FROM promo WHERE promo_name = :promo_name");
                  $this->promo->bind(":promo_name", $args[1]);
                  $this->promo->execute();
                  if (count($this->promo->get()) == 0) {
                    $this->promo->prepare("INSERT INTO promo (promo_name, promo_maxcount, promo_time, promo_value) VALUES (:promo_name, :promo_maxcount, :promo_time, :promo_value)");
                    $this->promo->bind(":promo_name", $args[1]);
                    $this->promo->bind(":promo_maxcount", $args[3]);
                    $this->promo->bind(":promo_time", time() + $args[2]);
                    $this->promo->bind(":promo_value", $args[4]);
                    $this->promo->execute();
                    $sender->sendMessage("Ты успешно создал промокод");
                  }
                  else {
                    $sender->sendMessage("Промокод уже существует!");
                  }
                }
                else {
                  $sender->sendMessage("Время/кол-во/приз должно быть числом!");
                }
              }
              else {
                $sender->sendMessage("Название должно состоять из букв и цифр!"); 
              }
            }
            else {
              $sender->sendMessage("/promo add [название] [время] [кол-во] [приз]");
            }
          }
          if ($args[0] == "remove" && $sender->isOp()) {
            if (isset($args[1])) {
              $this->promo->prepare("SELECT * FROM promo WHERE promo_name = :promo_name");
              $this->promo->bind(":promo_name", $args[1]);
              $this->promo->execute();
              if (count($this->promo->get()) != 0) {
                $this->promo->prepare("DELETE FROM promo WHERE promo_name = :promo_name");
                $this->promo->bind(":promo_name", $args[1]);
                $this->promo->execute();
                $sender->sendMessage("Ты успешно удалил промокод!");
              }
              else {
                $sender->sendMessage("Такого промокода не существует!");
              }
            }
            else {
              $sender->sendMessage("/remove [название]");
            }
          }
          if ($args[0] == "get") {
            if (isset($args[1])) {
              $this->promo->prepare("SELECT * FROM promo WHERE promo_name = :promo_name");
              $this->promo->bind(":promo_name", $args[1]);
              $this->promo->execute();
               if (count($this->promo->get()) != 0) {
                 $this->users->prepare("SELECT * FROM users WHERE promo_name = :promo_name AND name = :name");
                 $this->users->bind(":promo_name", $args[1]);
                 $this->users->bind(":name", $sender->getName());
                 $this->users->execute();
                 if (count($this->users->get()) == 0) {
                   $this->promo->prepare("SELECT * FROM promo WHERE promo_name = :promo_name");
                   $this->promo->bind(":promo_name", $args[1]);
                   $this->promo->execute();
                   $promo = $this->promo->get();
                   $promo_maxcount = $promo[1]["promo_maxcount"];
                   $promo_count = $promo[2]["promo_count"];
                   $promo_time = $promo[3]["promo_time"];
                   $promo_value = $promo[4]["promo_value"];
                   if ($promo_count >= $promo_maxcount) {
                     $sender->sendMessage("Промокод закончился!");
                   }
                   else {
                     if ($promo_time <= time()) {
                       $sender->sendMessage("Промокод закончился!");
                     }
                     else {
                       $this->promo->prepare("UPDATE promo SET promo_count = :promo_count WHERE promo_name = :promo_name");
                       $this->promo->bind(":promo_name", $args[1]);
                       $this->promo->bind(":promo_count", $promo_count + 1);
                       $this->promo->execute();
                       $this->users->prepare("INSERT INTO users (name, promo_name) VALUES (:name, :promo_name)");
                       $this->users->bind(":promo_name", $args[1]);
                       $this->users->bind(":name", $sender->getName());
                       $this->users->execute();
                       $eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
                       $eco->addMoney($sender->getName(), $promo_value);
                       $sender->sendMessage("Ты успешно использовал промокод, твой приз: {$promo_value}$");
                     }
                   }
                 }
                 else {
                   $sender->sendMessage("Ты уже использовал промокод!");
                 }
               }
              else {
                $sender->sendMessage("Такого промокода не существует!");
              }
            }
            else {
               $sender->sendMessage("/get [название]");
            }
          }
        }
        else {
          $sender->sendMessage("/promo [add|remove|get]");
        }
      break;
    }
    return true;
  }
  
}
