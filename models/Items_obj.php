<?php
	include 'Base.php';
	class Item extends Ordered{

		// Properties
		public $note;
		public $type = ITEM_TYPE;
		protected const ITEMS_TABLE = 'items';
		// Collection Properties
		protected const COLLECTION_ITEMS = 'collection_items';
		protected const PARENT_COLLECTION = 'parent_collection';
		protected const COLLECTION_TYPE =COLLECTION_TYPE;
  	// Subitem Properties
		protected const DEFAULT_NEW = ITEM_TYPE;
		protected const ITEM_ITEMS = 'item_items';
		protected const PARENT_ITEM = 'parent_item';
		protected const CHILD_ITEM = 'child_item';

		// Methods
		public function read(){
			try{
				$query = 'SELECT type, title, note, addTime, author_id FROM '.self::ITEMS_TABLE.' WHERE id = ? ';
				$stmt = $this->connection->prepare($query);

				$stmt->bindParam(1,$this->id);

				$stmt->execute();
				//fetch the record store in associate array
				$row = $stmt->fetch(PDO::FETCH_ASSOC);

				//assign each property the value in the corresponding field
				if($this->type !=$row['type']){
					throw new Exception("Wrong type!");
				}
				$this->title = $row['title'];
				$this->note = $row['note'];
				$this->addTime = $row['addTime'];
				$this->author_id = $row['author_id'];
				return true;
			}catch(Excepion $e){
				return false;
			}

		}

		public function create(){
			$query = 'INSERT INTO '.self::ITEMS_TABLE.'(type, title, note)'.
								'VALUES(:type, :title, :note)';
			try{
				$stmt = $this->connection->prepare($query);
  
				$this->title = (empty($this->title)) ? 'blank': htmlspecialchars($this->title);
				$this->note = htmlspecialchars($this->note);
  
				$stmt->bindParam(':type', $this->type);
				$stmt->bindParam(':title', $this->title,PDO::PARAM_STR);
				$stmt->bindParam(':note', $this->note,PDO::PARAM_STR);
  
				$stmt->execute();
				//get id
				$this->id = $this->connection->lastInsertId();
				return true;
			}catch(Exception $e){
				return false;
			}

		}

		public function update(){
			$query = 'UPDATE '.self::ITEMS_TABLE.
								' SET title = :title, note = :note'.
								' WHERE id = :id';
			try{
				$stmt = $this->connection->prepare($query);
				// Clean data
				$this->title = (empty($this->title)) ? 'blank': htmlspecialchars($this->title);
				$this->note = htmlspecialchars($this->note);
  
				// Bind parameters
				$stmt->bindParam(':title', $this->title,PDO::PARAM_STR);
				$stmt->bindParam(':note', $this->note,PDO::PARAM_STR);
				$stmt->bindParam(':id', $this->id);
  
				$stmt->execute();
				return true;
			}catch(Exception $e){
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

		public function addNewSubitem($author_id, $title, $item_type=self::DEFAULT_NEW){
			return $this->addNewSubItemGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $title, $item_type,$author_id);
		}

			// Read and store in subitems[]
		public function readSubitems(){
			$subItems = $this->readSubitemsGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, self::ITEMS_TABLE);
			if($subItems === false){
				return false;
			}else{
				$this->subItems = $subItems;
				return true;
			}
		}

		public function dropSubitem($item_id){
			return $this->dropSubItemGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $item_id);
		}

		public function deleteSubitem($item_id){
			return $this->deleteAllGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $item_id);
		}
		public function updateOrder(){
			return $this->updateOrderGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM);
		}
		public function moveSubItem($former_parent,$dragged_child){
			return $this->moveSubEleGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM,$former_parent,$dragged_child);
		}

	}// End Class Item

	class Check extends Item{
		// Properties
		public $type = CHECK_TYPE;
		public $checked;
		protected const CHECK_TABLE='checks';
		protected const DEFAULT_NEW = CHECK_TYPE;

		// Methods
		public function read(){
			try{
				$query = 'SELECT i.type, i.title, i.note, i.addTime, i.author_id , chk.checked FROM '.self::ITEMS_TABLE.' AS i'.
									' INNER JOIN '.self::CHECK_TABLE.' AS chk '.
									' ON i.id = chk.item_id '.
									' WHERE id = ? ';
				$stmt = $this->connection->prepare($query);

				$stmt->bindParam(1,$this->id);

				$stmt->execute();
				// fetch the record store in associate array
				$row = $stmt->fetch(PDO::FETCH_ASSOC);

				// assign each property the value in the corresponding field
				if($this->type != $row['type']){
					throw new Exception("Wrong type");
				}
				$this->title = $row['title'];
				$this->note = $row['note'];
				$this->addTime = $row['addTime'];
				$this->author_id = $row['author_id'];
				$this->checked = $row['checked'];
				return true;
			}catch(Exception $e){
				return false;
			}

		}

		public function create(){
			try{
				if(!parent::create()){
					throw new Exception("Fail to create item obj");	
				}
				$query = 'INSERT INTO '.self::CHECK_TABLE.' (item_id, checked) '.
										'VALUES(:item_id, DEFAULT)'; 
				// Prepare statement 
				$stmt = $this->connection->prepare($query);
        
				// Bind parameters
				$stmt->bindParam(':item_id', $this->id);

				$stmt->execute();
					return true;
			}catch(Exception $e){
				return false;
			}
			
		}// End create


		// recover it, since function inherient ref the self::DEFAULT_NEW in parent class where it is defined.
		public function addNewSubitem($author_id, $title, $item_type=self::DEFAULT_NEW){
			return $this->addNewSubItemGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $title, $item_type,$author_id);
		}
		
		public function checkTheBox($on_off){
			$query = 'UPDATE '.self::CHECK_TABLE.
								' SET checked = :checked '.
								' WHERE item_id = :id';
	
			try{
				$stmt = $this->connection->prepare($query);
				// Bind parameters
				$stmt->bindParam(':id', $this->id);
				$stmt->bindParam(':checked', $on_off, PDO::PARAM_BOOL);
	
				$stmt->execute();
				return true;
			}catch(Exception $e){
				return false;
			}
		}// End Check

	}//End Class Check

	class Task extends Check{
		// Properties
		public $type = TASK_TYPE;
		public $due;
		public $schedule;
		public $timer;
		public $totalTime;
		protected const TASK_TABLE='tasks';
		protected const DEFAULT_NEW =TASK_TYPE;
		
		// Methods
		public function read(){
			try{
				$query = 'SELECT i.type, i.title, i.note, i.addTime, i.author_id , chk.checked , tsk.schedule, tsk.due, tsk.totalTime FROM '.self::ITEMS_TABLE.' AS i'.
									' INNER JOIN '.self::CHECK_TABLE.' AS chk  ON i.id = chk.item_id '.
									' INNER JOIN '.self::TASK_TABLE.' AS tsk ON i.id = tsk.item_id '.
									' WHERE id = ? ';
				$stmt = $this->connection->prepare($query);

				$stmt->bindParam(1,$this->id);

				$stmt->execute();
				//fetch the record store in associate array
				$row = $stmt->fetch(PDO::FETCH_ASSOC);

				//assign each property the value in the corresponding field
				if($this->type != $row['type']){
					throw new Exception("Wrong type");
				}
				$this->title = $row['title'];
				$this->note = $row['note'];
				$this->addTime = $row['addTime'];
				$this->author_id = $row['author_id'];
				$this->checked = $row['checked'];
				$this->schedule= $row['schedule'];
				$this->due= $row['due'];
				$this->totalTime= $row['totalTime'];
				return true;
			}catch(Exception $e){
				return false;
			}

		}

		public function create(){
			try{
				if(!parent::create()){
					throw new Exception("Fail to create task obj");	
				}
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

				$stmt->execute();
					return true;
			}catch(Exception $e){
				return false;
			}
			
		}// End create

		public function update(){
			$query = 'UPDATE '.self::ITEMS_TABLE.' AS i'.
								' INNER JOIN '.self::TASK_TABLE.' AS tsk ON (i.id = tsk.item_id)'.
								' SET i.title = :title, i.note = :note, tsk.schedule = :schedule, tsk.due = :due'.
								' WHERE i.id = :id';

			$stmt = $this->connection->prepare($query);
			// Clean data
			$this->title = (empty($this->title)) ? 'blank': htmlspecialchars($this->title);
			$this->note = htmlspecialchars($this->note);

			// Bind parameters
			$stmt->bindParam(':title', $this->title);
			$stmt->bindParam(':note', $this->note);
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

				// recover it, since function inherient ref the self::DEFAULT_NEW in parent class where it is defined.
		public function addNewSubitem($author_id, $title, $item_type=self::DEFAULT_NEW){
			return $this->addNewSubItemGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $title, $item_type,$author_id);
		}
	}// End Task
?>
