<?php
	/*	project: list
	 *	description: define the basic element for list. extent to more functional version.
	 */
	
	class Item{
		//Database
		private $connection;
		private $table='items';
		
		//Item Properties
		public $id;
		public $type = 0;//(0:Item, 1:check, 2:task)
		public $title;
		public $note;
		public $addtime;
		public $user_id;

		//Constructor with Database
		public function __construct($pdoObj){
			
			$this->connection = $pdoObj;
			
		}

		//Get Item from DB
		public function read(){
			// SQL query: find the item by id from items table
			$query = 'SELECT type, title, note, addtime, user_id FROM '.$this->table.'WHERE id = ? LIMIT 0,1';

			// Prepare statement 
			$stmt = $this->connection->prepare($query);

			// Bind ? to ID
			$stmt->bindParam(1, $this->id);
			
			// Execute the statement to get the result of the query
			$stmt->execute();
			
			//fetch the record store in associate array
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			//assign each property the value in the corresponding field
			$this->type = $row['type'];
			$this->title = $row['title'];
			$this->note = $row['note'];
			$this->addtime = $row['addtime'];
			$this->user_id = $row['user_id'];
		}//End read

		// Create Item in DB
		public function create(){
			// SQL query: add a new item to items
			$query = 'INSERT INTO '.$this->table.'(type, title, note, user_id) '.
									'VALUES(:type, :title, :note, :user_id)';

			// Prepare statement 
			$stmt = $this->connection->prepare($query);

			// Clean data ( maybe do this when api get data from user QQ_security)
			$this->type = htmlspecialchars(strip_tags($this->type));
			$this->title = htmlspecialchars(strip_tags($this->title));
			$this->note = htmlspecialchars(strip_tags($this->note));
			$this->user_id = htmlspecialchars(strip_tags($this->user_id));

			// Bind parameters
			$stmt->bindParam(':type', $this->type);
			$stmt->bindParam(':title', $this->title);
			$stmt->bindParam(':note', $this->note);
			$stmt->bindParam(':user_id', $this->user_id);

			
			// Execute the statement. Check if it success inserting record. 
			if($stmt->execute()){
				// get the id
				$result = $this->connection->query('SELECT LAST_INSERT_ID()', PDO::FETCH_NUM);//useing num since it only has one field
				$this->id = $result[0];
				return true;
			}else{
				echo "Error:".$stmt->error."<br/>";
				return false;
			}
		}//End create
	
		//Update Item in DB
		public function update(){
			// SQL query: find the item by id from items and update it
			$query = 'UPDATE '.$thit->table.
								'SET title = :title, note = :note, user_id = :user_id'.
								'WHERE id = :id';

			// Prepare statement 
			$stmt = $this->connection->prepare($query);

			// Clean data ( maybe do this when api get data from user QQ_security)
			$this->title = htmlspecialchars(strip_tags($this->title));
			$this->note = htmlspecialchars(strip_tags($this->note));
			$this->user_id = htmlspecialchars(strip_tags($this->user_id));
			$this->id = htmlspecialchars(strip_tags($this->id));//QQ_neccessary?

			// Bind parameters
			$stmt->bindParam(':title', $this->title);
			$stmt->bindParam(':note', $this->note);
			$stmt->bindParam(':user_id', $this->user_id);
			$stmt->bindParam(':id', $this->id);//QQ_neccessary?
			
			// Execute the statement. Check if it success inserting record. 
			if($stmt->execute()){
				return true;
			}else{
				echo "Error:".$stmt->error."<br/>";//QQ_where to echo? log? <br/>?
				return false;
			}
		}//End update

		// Delete item from DB
		public function delete(){
			// SQL query: delete item from items by id
			$query = 'DELETE FROM '.$this->table.
									'WHERE id = :id)';

			// Prepare statement 
			$stmt = $this->connection->prepare($query);

			// Clean data ( maybe do this when api get data from user QQ_security)
			$this->id = htmlspecialchars(strip_tags($this->id));//QQ_neccessary?

			// Bind parameters
			$stmt->bindParam(':id', $this->id);//QQ_neccessary?

			
			// Execute the statement. Check if it success inserting record. 
			if($stmt->execute()){
				return true;
			}else{
				echo "Error:".$stmt->error."<br/>";
				return false;
			}
		}//end delete

}//End Item

	class Check extends Item{
		//Item Properties
		public $type = 1;//(0:Item, 1:check, 2:task)
		private $check_table='checks';
		public $check;

		//Get Item from DB: Override Item.read()
		public function read(){
			// SQL query: find the item by id from items table
			$query = 'SELECT i.type, i.title, i.note, i.addtime, i.user_id , chk.check'.
								'FROM '.$this->table.'AS i '.
								'INNER JOIN'.$this->check_table.'AS chk '.
								'ON i.id = chk.item_id '.
								'WHERE i.id = ? LIMIT 0,1';

			// Prepare statement 
			$stmt = $this->connection->prepare($query);

			// Bind ? to ID
			$stmt->bindParam(1, $this->id);
			
			// Execute the statement to get the result of the query
			$stmt->execute();
			
			//fetch the record store in associate array
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			//assign each property the value in the corresponding field
			$this->type = $row['type'];
			$this->title = $row['title'];
			$this->note = $row['note'];
			$this->addtime = $row['addtime'];
			$this->user_id = $row['user_id'];
			$this->check = $row['check'];
		}//End read

		// Create an item in DB and then add the additional info in table:check
		public function create(){
			// Call Item.create(), adding item first
			if(parent::create()){
				// SQL query: Add id and check to table:checks
				$query = 'INSERT INTO '.$this->check_table.'(item_id, check) '.
										'VALUES(:item_id, :check)';
        
				// Prepare statement 
				$stmt = $this->connection->prepare($query);
        
				// Clean data ( maybe do this when api get data from user QQ_security)
				$this->item_id = htmlspecialchars(strip_tags($this->id));//already get the id by executing Item.create().
				$this->check = htmlspecialchars(strip_tags($this->check));
        
				// Bind parameters
				$stmt->bindParam(':item_id', $this->item_id);
				$stmt->bindParam(':check', $this->check);
        
				
				// Execute the statement. Check if it success inserting record. 
				if($stmt->execute()){
					return true;
				}else{
					echo "Error:".$stmt->error."<br/>";
					return false;
				}
			}
		}//End create
	
		//Update data in DB (Override the function Item.update())
		//QQ_check 跟 update 分開實做會不會比較好？這樣就不用重作整個update(),分開有比較快？
		//AA_分別實做！
		public function update(){
			// SQL query: find the item by id from items join table:checks and update it
			$query = 'UPDATE '.$this->table.'AS i'.
								'INNERJOIN '.$this->check_table.'AS chk ON (i.id = chk.item_id)'.
								'SET i.title = :title, i.note = :note, i.user_id = :user_id, chk.check = :check'.
								'WHERE i.id = :id';

			// Prepare statement 
			$stmt = $this->connection->prepare($query);

			// Clean data ( maybe do this when api get data from user QQ_security)
			$this->title = htmlspecialchars(strip_tags($this->title));
			$this->note = htmlspecialchars(strip_tags($this->note));
			$this->user_id = htmlspecialchars(strip_tags($this->user_id));//QQ_neccessary?
			$this->id = htmlspecialchars(strip_tags($this->id));//QQ_neccessary?

			// Bind parameters
			$stmt->bindParam(':title', $this->title);
			$stmt->bindParam(':note', $this->note);
			$stmt->bindParam(':user_id', $this->user_id);
			$stmt->bindParam(':id', $this->id);//QQ_neccessary?
			$stmt->bindParam(':check', $this->check);
			
			// Execute the statement. Check if it success inserting record. 
			if($stmt->execute()){
				return true;
			}else{
				echo "Error:".$stmt->error."<br/>";
				return false;
			}
		}//End update

		// Delete info in table:checks using SQL setting 
		// "CONSTRANT 'fk_check_item' FOREIGN KEY (item_id) REFERENCES items (id) ON DELETE CASCADE"
		// Use the Item.delete()

		// Check the item (update check)
		public function check(){
			// SQL query: find the item by id from items join table:checks and update it
			$query = 'UPDATE '.$this->check_table.
								'SET check = :check'.
								'WHERE item_id = :id';

			// Prepare statement 
			$stmt = $this->connection->prepare($query);

			// Clean data ( maybe do this when api get data from user QQ_security)
			$this->id = htmlspecialchars(strip_tags($this->id));//QQ_neccessary?

			// Bind parameters
			$stmt->bindParam(':id', $this->id);//QQ_neccessary?
			$stmt->bindParam(':check', $this->check);
			
			// Execute the statement. Check if it success inserting record. 
			if($stmt->execute()){
				return true;
			}else{
				echo "Error:".$stmt->error."<br/>";
				return false;
			}
		}//End check

	}//end class Check

	class Task extends Check{
		//Item Properties
		public $type = 2;//(0:Item, 1:check, 2:task)
		private $task_table='tasks';
		public $shedule;
		public $due;
		public $startTime;
		public $endTime;

		//Get Item from DB: Override Item.read()
		public function read(){
			// SQL query: find the item by id from items table
			$query = 'SELECT i.type, i.title, i.note, i.addtime, i.user_id , chk.check, sk9.schedule, sk9.due, sk9.startTime,sk9.endTime'.
								'FROM '.$this->table.'AS i '.
								'INNER JOIN'.$this->check_table.'AS chk '.'ON i.id = chk.item_id '.
								'INNER JOIN'.$this->task_table.'AS sk9 '.'ON i.id = sk9.item_id '.
								'WHERE i.id = ? LIMIT 0,1';

			// Prepare statement 
			$stmt = $this->connection->prepare($query);

			// Bind ? to ID
			$stmt->bindParam(1, $this->id);
			
			// Execute the statement to get the result of the query
			$stmt->execute();
			
			//fetch the record store in associate array
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			//assign each property the value in the corresponding field
			$this->type = $row['type'];
			$this->title = $row['title'];
			$this->note = $row['note'];
			$this->addtime = $row['addtime'];
			$this->user_id = $row['user_id'];
			$this->check = $row['check'];
			$this->schedule= $row['schedule'];
			$this->due= $row['due'];
			$this->startTime= $row['startTime'];
			$this->endTime= $row['endTime'];
		}//End read

		// Create an item in DB and then add the additional info in table:check
		public function create(){
			// Call Check.create(), adding item first
			if(parent::create()){
				// SQL query: Add id and data to table:tasks
				$query = 'INSERT INTO '.$this->task_table.'(item_id, schedule, due, startTime, endTime) '.
										'VALUES(:item_id, :schedule, :due, :startTime, :endTime)';
        
				// Prepare statement 
				$stmt = $this->connection->prepare($query);
        
				// Clean data ( maybe do this when api get data from user QQ_security)
				$this->item_id = htmlspecialchars(strip_tags($this->id));//already get the id by executing Item.create().
				$this->schedule = htmlspecialchars(strip_tags($this->schedule));
				$this->due = htmlspecialchars(strip_tags($this->due));
				$this->startTime = htmlspecialchars(strip_tags($this->startTime));
				$this->endTime = htmlspecialchars(strip_tags($this->endTime));
        
				// Bind parameters
				$stmt->bindParam(':item_id', $this->item_id);
				$stmt->bindParam(':schedule', $this->schedule);
				$stmt->bindParam(':due', $this->due);
				$stmt->bindParam(':startTime', $this->startTime);
				$stmt->bindParam(':endTime', $this->endTime);
        
				
				// Execute the statement. Check if it success inserting record. 
				if($stmt->execute()){
					return true;
				}else{
					echo "Error:".$stmt->error."<br/>";
					return false;
				}
			}
		}//End create
	
		//Update data in DB (Using the method from parent)
		//QQ_check 跟 update 分開實做會不會比較好？這樣就不用重作整個update(),分開有比較快？
		//AA_分別都做 update 全部更新 其他的部份更新

		public function update(){
			// SQL query: find the item by id from items join table:checks,tasks and update it
			$query = 'UPDATE '.$this->table.'AS i'.
								'INNERJOIN '.$this->check_table.'AS chk ON (i.id = chk.item_id)'.
								'INNERJOIN '.$this->task_table.'AS sk9 ON (i.id = sk9.item_id)'.
								'SET i.title = :title, i.note = :note, i.user_id = :user_id, chk.check = :check, sk9.schedule = :schedule, sk9.due = :due, sk9.startTime = :startTime, sk9.endTime = :endTime'.
								'WHERE i.id = :id';

			// Prepare statement 
			$stmt = $this->connection->prepare($query);

			// Clean data ( maybe do this when api get data from user QQ_security)
			$this->title = htmlspecialchars(strip_tags($this->title));
			$this->note = htmlspecialchars(strip_tags($this->note));
			$this->user_id = htmlspecialchars(strip_tags($this->user_id));//QQ_neccessary?
			$this->id = htmlspecialchars(strip_tags($this->id));//QQ_neccessary?
			$this->schedule = htmlspecialchars(strip_tags($this->schedule));//QQ_neccessary?
			$this->due = htmlspecialchars(strip_tags($this->due));//QQ_neccessary?
			$this->startTime = htmlspecialchars(strip_tags($this->startTime));//QQ_neccessary?
			$this->endTime = htmlspecialchars(strip_tags($this->endTime));//QQ_neccessary?

			// Bind parameters
			$stmt->bindParam(':title', $this->title);
			$stmt->bindParam(':note', $this->note);
			$stmt->bindParam(':user_id', $this->user_id);
			$stmt->bindParam(':id', $this->id);
			$stmt->bindParam(':check', $this->check);
			$stmt->bindParam(':schedule', $this->schedule);
			$stmt->bindParam(':due', $this->due);
			$stmt->bindParam(':startTime', $this->startTime);
			$stmt->bindParam(':endTime', $this->endTime);
			
			// Execute the statement. Check if it success inserting record. 
			if($stmt->execute()){
				return true;
			}else{
				echo "Error:".$stmt->error."<br/>";
				return false;
			}
		}//End update

		// Check the item (update check and endTime)
		public function check(){
			// SQL query: find the item by id from items join table:checks and update it
			$query = 'UPDATE '.$this->check_table.'AS chk'.
								'INNER JOIN'.$this->task_table.'AS sk9'.
								'SET chk.check = :check, sk9.endTime = :endTime'.
								'WHERE item_id = :id';

			// Prepare statement 
			$stmt = $this->connection->prepare($query);

			// Clean data ( maybe do this when api get data from user QQ_security)
			$this->id = htmlspecialchars(strip_tags($this->id));//QQ_neccessary?
			$this->endTime = htmlspecialchars(strip_tags($this->endTime));//QQ_neccessary?

			// Bind parameters
			$stmt->bindParam(':id', $this->id);//QQ_neccessary?
			$stmt->bindParam(':check', $this->check);
			$stmt->bindParam(':endTime', $this->endTime);
			
			// Execute the statement. Check if it success inserting record. 
			if($stmt->execute()){
				return true;
			}else{
				echo "Error:".$stmt->error."<br/>";
				return false;
			}
		}//end check

		// Schedule (update part of data: schedule due startTime)
		public function schedule(){
			// SQL query: find the item by id from table:tasks and update it
			$query = 'UPDATE '.$this->task_table.'AS sk9'.
								'SET sk9.schedule = :schedule, sk9.due = :due, sk9.startTime = :startTime'.
								'WHERE i.id = :id';

			// Prepare statement 
			$stmt = $this->connection->prepare($query);

			// Clean data ( maybe do this when api get data from user QQ_security)
			$this->id = htmlspecialchars(strip_tags($this->id));//QQ_neccessary?
			$this->schedule = htmlspecialchars(strip_tags($this->schedule));//QQ_neccessary?
			$this->due = htmlspecialchars(strip_tags($this->due));//QQ_neccessary?
			$this->startTime = htmlspecialchars(strip_tags($this->startTime));//QQ_neccessary?

			// Bind parameters
			$stmt->bindParam(':id', $this->id);
			$stmt->bindParam(':schedule', $this->schedule);
			$stmt->bindParam(':due', $this->due);
			$stmt->bindParam(':startTime', $this->startTime);
			
			// Execute the statement. Check if it success inserting record. 
			if($stmt->execute()){
				return true;
			}else{
				echo "Error:".$stmt->error."<br/>";
				return false;
			}
		}//end schedule

		// Delete info in table:checks and table:tasks using SQL setting 
		// "CONSTRANT 'fk_check_item' FOREIGN KEY (item_id) REFERENCES items (id) ON DELETE CASCADE" in table:checks
		// "CONSTRANT 'fk_task_item' FOREIGN KEY (item_id) REFERENCES items (id) ON DELETE CASCADE" in table:tasks
		// Use the Item.delete()
		}// end Class task
	
?>
