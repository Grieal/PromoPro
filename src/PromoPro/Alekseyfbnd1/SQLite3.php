<?php

namespace PromoPro\Alekseyfbnd1;

class SQLite3 {
	
  private $plugin;
  private $database;
  private $base_prepare;
  private $base_result;
	
  public function __construct($plugin, string $name) {
    $this->plugin = $plugin;
    $this->database = new \SQLite3($this->plugin->getDataFolder().$name.".db");
    $this->createTable($name);
  }
	
  public function createTable(string $name) : void {
    $this->database->exec(stream_get_contents($this->plugin->getResource($name.".sql")));
  }
	
  public function prepare(string $query) : void {
    $this->base_prepare = $this->database->prepare($query);
  }
	
  public function bind(string $name, $value) : void {
    $this->base_prepare->bindValue($name, $value);
  }
	
  public function execute() : void {
    $this->base_result = $this->base_prepare->execute();
  }
	
  public function get() : array {
    $row = array();
    $i = 0;
    while($res = $this->base_result->fetchArray(SQLITE3_ASSOC)) {
      $row[$i] = $res;
      $i++;
    }
    return $row;
  }
}
