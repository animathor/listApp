<?php
include'Items.php';
	class BasicList {
		//DB
		private $connection;
		private $table = 'lists'
		//List properties
		public $head;
		public $items;
		public $length;

		//Constructor
		public function __construct($database, $title){
			$this->connection = $database;
			$this->head = new Item($database);
			$this->head->title = $title;
		}
		
		public function addNewItem($title){
			$this->items[] = new Item($this->connection);
			
		}
		
		
	}
?>
