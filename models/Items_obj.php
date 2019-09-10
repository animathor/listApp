<?php
	include_once 'Base.php';
	class Item extends Ordered{

		// Properties
		public $note;
		public $type = 2;
		protected const ITEMS_TABLE = 'items';
		// Collection Properties
		protected const COLLECTION_ITEMS = 'collection_items';
		protected const PARENT_COLLECTION = 'parent_collection';
		protected const COLLECTION_TYPE =1;
  	// Subitem Properties
		protected const DEFAULT_NEW = 2;
		protected const ITEM_ITEMS = 'item_items';
		protected const PARENT_ITEM = 'parent_item';
		protected const CHILD_ITEM = 'child_item';

		// Methods
		public function read(){
			$query = 'SELECT type, title, note, addTime, author_id FROM '.self::ITEMS_TABLE.' WHERE id = ? ';
			$stmt = $this->connection->prepare($query);

			$stmt->bindParam(1,$this->id);

			if($stmt->execute()){
				//fetch the record store in associate array
				$row = $stmt->fetch(PDO::FETCH_ASSOC);

				//assign each property the value in the corresponding field
				$this->type = $row['type'];
				$this->title = $row['title'];
				$this->note = $row['note'];
				$this->addTime = $row['addTime'];
				$this->author_id = $row['author_id'];
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}

		}

		public function create(){
			$query = 'INSERT INTO '.self::ITEMS_TABLE.'(type, title, note)'.
								'VALUES(:type, :title, :note)';
			$stmt = $this->connection->prepare($query);

			$this->title = htmlspecialchars(strip_tags($this->title));
			$this->note = htmlspecialchars(strip_tags($this->note));

			$stmt->bindParam(':type', $this->type);
			$stmt->bindParam(':title', $this->title);
			$stmt->bindParam(':note', $this->note);

			if($stmt->execute()){
				//get id
				$stmt = $this->connection->query('SELECT LAST_INSERT_ID()');
				$result = $stmt->fetch(PDO::FETCH_NUM);
				$this->id = $result[0];
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}

		}

		public function update(){
			$query = 'UPDATE '.self::ITEMS_TABLE.
								' SET title = :title, note = :note'.
								' WHERE id = :id';
			$stmt = $this->connection->prepare($query);
			// Clean data
			$this->title = htmlspecialchars(strip_tags($this->title));
			$this->note = htmlspecialchars(strip_tags($this->note));
			//$this->author_id = htmlspecialchars(strip_tags($this->author_id));

			// Bind parameters
			$stmt->bindParam(':title', $this->title);
			$stmt->bindParam(':note', $this->note);
			$stmt->bindParam(':id', $this->id);

			if($stmt->execute()){
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
		}
		
		public function delete(){
			return $this->deleteAllGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, self::ITEMS_TABLE, $this->id);
		}//end delete
	
	// Collection methods
		public function in_collection(){
			return $this->readSupEle(self::COLLECTION_ITEMS, self::PARENT_COLLECTION, self::CHILD_ITEM,$this->id,self::COLLECTION_TYPE);
		}
	
	// Supitem methods
		public function traceBack(){
			return $this->traceBackVEle(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, self::ITEMS_TABLE,$this->id);
		}
	
	// Subitem methods
		public function addSubitem($item_id){
			return $this->addSubItemGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $item_id);
		}

		public function addNewSubitem($title, $item_type=self::DEFAULT_NEW){
			return $this->addNewSubItemGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $title, $item_type);
		}

			// Read and store in subitems[]
		public function readSubitems(){
			return $this->readSubitemsGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, self::ITEMS_TABLE);
		}

		public function dropSubitem($item_id){
			return $this->dropSubItemGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $item_id);
		}

		public function deleteSubitem($item_id){
			return $this->deleteAllGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $item_id);
		}
		public function updateOrder(){
			return $this->updateOrderGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, self::ITEMS_TABLE);
		}

	}// End Class Item

	class Check extends Item{
		// Properties
		public $type = 4;
		public $checked;
		protected const CHECK_TABLE='checks';
		protected const DEFAULT_NEW = 4;

		// Methods
		public function read(){
			$query = 'SELECT i.type, i.title, i.note, i.addTime, i.author_id , chk.checked FROM '.self::ITEMS_TABLE.' AS i'.
								' INNER JOIN '.self::CHECK_TABLE.' AS chk '.
								' ON i.id = chk.item_id '.
								' WHERE id = ? ';
			$stmt = $this->connection->prepare($query);

			$stmt->bindParam(1,$this->id);

			if($stmt->execute()){
				//fetch the record store in associate array
				$row = $stmt->fetch(PDO::FETCH_ASSOC);

				//assign each property the value in the corresponding field
				$this->type = $row['type'];
				$this->title = $row['title'];
				$this->note = $row['note'];
				$this->addTime = $row['addTime'];
				$this->author_id = $row['author_id'];
				$this->checked = $row['checked'];
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}

		}

		public function create(){
			if(parent::create()){
				$query = 'INSERT INTO '.self::CHECK_TABLE.' (item_id, checked) '.
										'VALUES(:item_id, DEFAULT)';
        
				// Prepare statement 
				$stmt = $this->connection->prepare($query);
        
				// Bind parameters
				$stmt->bindParam(':item_id', $this->id);

				if($stmt->execute()){
					return true;
				}else{
					foreach($stmt->errorInfo() as $line)
						echo $line."</br>";
					return false;
				}
			}
		}// End create

		public function update(){
			$query = 'UPDATE '.self::ITEMS_TABLE.' AS i'.
								' INNER JOIN '.self::CHECK_TABLE.' AS chk ON (i.id = chk.item_id) '.
								' SET i.title = :title, i.note = :note, chk.checked = :checked '.
								' WHERE i.id = :id';

			$stmt = $this->connection->prepare($query);
			// Clean data
			$this->title = htmlspecialchars(strip_tags($this->title));
			$this->note = htmlspecialchars(strip_tags($this->note));

			// Bind parameters
			$stmt->bindParam(':title', $this->title);
			$stmt->bindParam(':note', $this->note);
			$stmt->bindParam(':id', $this->id);
			$stmt->bindParam(':checked', $this->checked,PDO::PARAM_BOOL);

			if($stmt->execute()){
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
		}
		
		// recover it, since function inherient ref the self::DEFAULT_NEW in parent class where it is defined.
		public function addNewSubitem($title, $item_type=self::DEFAULT_NEW){
			return $this->addNewSubItemGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $title, $item_type);
		}
/*
		public function check($on_off){
			$query = 'UPDATE '.self::CHECK_TABLE.
								' SET checked = :checked '.
								' WHERE item_id = :id';
	try{
					throw new Exception(var_dump($this->connection));
			}catch(Exception $e){
				print "檔案:".$e->getFile()."<br/>";
				print "行號".$e->getLine()."<br/>";
				print "錯誤:".$e->getMessage()."<br/>";
			}
			$stmt = $this->connection->prepare($query);
			// Bind parameters
			$stmt->bindParam(':id', $this->id);
			$stmt->bindParam(':checked', $on_off, PDO::PARAM_BOOL);
	
			if($stmt->execute()){
				// if the item turn off, so does supitem
				if($on_off === false){
					$supitem = $this->readSupitemGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, self::ITEMS_TABLE);
					if($supitem->checked === true){
						$supitem->check(false);
					}
				}else{
					// if item is checked, Subitems follows item
					$this->readSubitems();
					foreach($this->subitems as $subitem){
						$subitem->check(true);
					}
				}
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
		}// End Check
*/
	}//End Class Check

	class Task extends Check{
		// Properties
		public $type = 6;
		public $due;
		public $schedule;
		public $timer;
		public $totalTime;
		protected const TASK_TABLE='tasks';
		protected const DEFAULT_NEW = 6;
		
		// Methods
		public function read(){
			$query = 'SELECT i.type, i.title, i.note, i.addTime, i.author_id , chk.checked , tsk.schedule, tsk.due, tsk.totalTime FROM '.self::ITEMS_TABLE.' AS i'.
								' INNER JOIN '.self::CHECK_TABLE.' AS chk  ON i.id = chk.item_id '.
								' INNER JOIN '.self::TASK_TABLE.' AS tsk ON i.id = tsk.item_id '.
								' WHERE id = ? ';
			$stmt = $this->connection->prepare($query);

			$stmt->bindParam(1,$this->id);

			if($stmt->execute()){
				//fetch the record store in associate array
				$row = $stmt->fetch(PDO::FETCH_ASSOC);

				//assign each property the value in the corresponding field
				$this->type = $row['type'];
				$this->title = $row['title'];
				$this->note = $row['note'];
				$this->addTime = $row['addTime'];
				$this->author_id = $row['author_id'];
				$this->checked = $row['checked'];
				$this->schedule= $row['schedule'];
				$this->due= $row['due'];
				$this->totalTime= $row['totalTime'];
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}

		}

		public function create(){
			if(parent::create()){
				$query = 'INSERT INTO '.self::TASK_TABLE.' (item_id, schedule, due) '.
										'VALUES(:item_id, :schedule, :due)';
        
				// Prepare statement 
				$stmt = $this->connection->prepare($query);
        
				// Bind parameters
				if(empty($this->schedule))
					$this->schedule = null;
				if(empty($this->due))
					$this->due = null;
				
				$stmt->bindParam(':item_id', $this->id);
				$stmt->bindParam(':schedule', $this->schedule);
				$stmt->bindParam(':due', $this->due);

				if($stmt->execute()){
					return true;
				}else{
					foreach($stmt->errorInfo() as $line)
						echo $line."</br>";
					return false;
				}
			}
		}// End create

		public function update(){
			$query = 'UPDATE '.self::ITEMS_TABLE.' AS i'.
								' INNER JOIN '.self::CHECK_TABLE.' AS chk ON (i.id = chk.item_id) '.
								' INNER JOIN '.self::TASK_TABLE.' AS tsk ON (i.id = tsk.item_id)'.
								' SET i.title = :title, i.note = :note, chk.checked = :checked, tsk.schedule = :schedule, tsk.due = :due'.
								' WHERE i.id = :id';

			$stmt = $this->connection->prepare($query);
			// Clean data
			$this->title = htmlspecialchars(strip_tags($this->title));
			$this->note = htmlspecialchars(strip_tags($this->note));

			// Bind parameters
			$stmt->bindParam(':title', $this->title);
			$stmt->bindParam(':note', $this->note);
			$stmt->bindParam(':checked', $this->checked,PDO::PARAM_BOOL);
			$stmt->bindParam(':schedule', $this->schedule);
			$stmt->bindParam(':due', $this->due);
			$stmt->bindParam(':id', $this->id);

			if($stmt->execute()){
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
		}

		//check
		public function check($on_off){
			$query = 'UPDATE '.self::CHECK_TABLE.' AS chk'.
								' INNER JOIN '.self::TASK_TABLE.' AS tsk ON tsk.item_id = chk.item_id'.
								' SET chk.checked = :checked , tsk.totlaTime = :totlaTime'.
								' WHERE chk.item_id = :id';

			$stmt = $this->connection->prepare($query);
			// Bind parameters
			$stmt->bindParam(':id', $this->id);
			$stmt->bindParam(':totlaTime', $this->totlaTime);
			$stmt->bindParam(':checked', $on_off, PDO::PARAM_BOOL);

			if($stmt->execute()){
				// if the item turn off, so does supitem
				if($on_off === false){
					$supitem = $this->readSupitemGenn(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, self::ITEMS_TABLE);
					if($supitem->checked === true){
						$supitem->check(false);
					}
				}else{
					// if item is checked, Subitems follows item
					$this->readSubitems();
					foreach($this->subitems as $subitem){
						$subitem->check(true);
					}
				}
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
		}// End Check

		//clock()
		protected function clock(){
			$query = 'UPDATE '.self::TASK_TABLE.
								' SET timer = :timer'.
								' WHERE item_id = :id';

			$stmt = $this->connection->prepare($query);
			// Bind parameters
			$stmt->bindParam(':id', $this->id);
			$stmt->bindParam(':timer', $this->timer);

			if($stmt->execute()){
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
		}
		
				// recover it, since function inherient ref the self::DEFAULT_NEW in parent class where it is defined.
		public function addNewSubitem($title, $item_type=self::DEFAULT_NEW){
			return $this->addNewSubItemGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $title, $item_type);
		}
	}// End Task
?>