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
		public $type;
		public $title;
		public $note;
		public $addtime;
		public $user_id;

		//Constructor with Database
		public function __construct($database){
			
			$this->connection = $database;
			
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
			$stmt->bindParam(':addtime', $this->addtime);
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
				echo "Error:".$stmt->error."<br/>";
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
				$this->item_id = htmlspecialchars(strip_tags($this->item_id));
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

	}//end class Check

	class Task extends Check{
		//Item Properties
		private $task_table='tasks';
		public $shedule;
		public $due;
		public $startTime;
		public $endTime;

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
				$this->item_id = htmlspecialchars(strip_tags($this->item_id));
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

?>
