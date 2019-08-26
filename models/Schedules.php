<?php
	include_once 'Items_obj.php';

	class Schedule extends Order{
		// Properties
		public $due;
		public $lifeTime;
		private const TYPE = 3;
		private const SCHEDULE_TABLE = 'schedules';
		private const ITEM_TABLE ='items';

		// Subitems Properties
		private const SCHEDULE_ITEMS = 'schedule_items';
		private const PARENT_SCHEDULE = 'parent_schedule';
		private const CHILE_ITEM = 'child_item';
		private const DEFAULT_NEW = 6;// task

		private const ITEM_ITEMS = 'item_items';
		private const PARENT_ITEM = 'parent_item';


		// Methods
		public function read(){
			$query = 'SELECT type, title, addTime, due, lifeTime FROM '.self::SCHEDULE_TABLE.' WHERE id = ? ';
			$stmt = $this->connection->prepare($query);

			$stmt->bindParam(1,$this->id);

			if($stmt->execute()){
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
		}

		public function creat(){
			$query = 'INSERT INTO '.self::SCHEDULE_TABLE.'(type, title, due, lifeTime)'.
								'VALUES(:type, :title, :due, :lifeTime)';
			$stmt = $this->connection->prepare($query);

			$stmt->bindParam(':type', self::TYPE);
			$stmt->bindParam(':title', $this->title);
			$stmt->bindParam(':due', $this->due);
			$stmt->bindParam(':lifeTime', $this->lifeTime);

			if($stmt->execute()){
				//get id
				$stmt->$this->connection->query('SELECT LAST_INSERT_ID()');
				$result = $stmt->fetch(PDO::FETCH_NUM);
				$this->id = $result[0];
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
		}
		public function delete(){
			return $this->deleteEle(self::SCHEDULE_ITEMS, $this->id);
		}

		// Subitems Methods

		public function addTask($item_id){
			return $this->addSubItemGen(self::SCHEDULE_ITEMS, self::PARENT_SCHEDULE, self::CHILD_ITEM, $item_id);
		}

		public function addNewTask($item_type, $title){
			return $this->addNewSubItemGen($item_type, $title);
		}

			// Read and store in subitems[]
		public function readTasks(){
			return $this->readSubitemsGen(self::SCHEDULE_ITEMS, self::PARENT_SCHEDULE, self::CHILD_ITEM, self::ITEMS_TABLE);
		}

		public function dropTask($item_id){
			return $this->dropSubItemGen(self::SCHEDULE_ITEMS, self::PARENT_SCHEDULE, self::CHILD_ITEM, $item_id);
		}

		public function deleteTask($item_id){
			return $this->deleteAllGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $item_id);
		}


	}
