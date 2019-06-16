<?php
include'Items.php';
	class BasicList {
		//DB
		private $connection;
		private $table = 'lists';
		private $listItems_table = 'list_items';
		//List properties
		public $id;
		public $head;
		public $items;
		public $length=0;
		private $lastAddIndex=0;

		//Constructor
		public function __construct($pdoObj){
			$this->connection = $pdoObj;
		}
		
		// Create empty list 
		public function create($listTitle){
			$newHead = new Item($this->connection);
			$newHead->title = htmlspecialchars(strip_tags($listTitle));
			if($newHead->create()){
				$this->head = $newHead;
				// add record in table:lists
				$query = 'INSERT INTO '.$this->table.'(head_id) VALUES(:head_id)';
				$stmt = $this->connection->prepare($query);
				$stmt->bindParam(':head_id',$this->head->id);
				if($stmt->execute()){
					$result = $this->connection->query('SELECT LAST_INSERT_ID()', PDO::FETCH_NUM);//useing num since it only has one field
					$this->id = $result[0];//store lists id
					return true;
				}else{
					//destroy the head item
					$this->head->delete();
					$this->head = null;
					return false;
				}

			}else{
				return false;
			}
		}
		
		public function addNewItem($title){
			// you can't add item before you have the list(record in DB)QQ_neccessary?
			if(!is_null($this->id)){
				// Create item in DB, get id.
				$newItem = new Item($this->connection);
				$newItem->title = $title;
				if($newItem->create()){
					//store in items[]
					items[] = $newItem;
					//update lastAddIndex
					//QQ_為了取得 item 的 index 可以用 key(end($items)) , array_search($newItem, $items) 來取得，最後決定單純一點用var存lastAddIndex
					if($this->lastAddIndex >0)
						$lastAddIndex++;
          
					// Add new list relation
					$query = 'INSERT INTO '.$this->table.
										'SET head__id = :head_id, item_id = :item_id, item_ordinalNum = :item_ordinalNum';
					
					// Prepare statement
					$stmt = $this->connection->prepare($query);
      
					// Bind Parameters
					$stmt->bindParam(':head_id', $this->head->id);
					$stmt->bindParam(':item_id', $newItem->id);//QQ_ same as $this->items[$lastAddItem]->id but...
					$stmt->bindParam(':item_ordinalNum', $lastAddItem);//QQ_make sure $lastAddItem == index of newItem in items
      
					// Execute statement
					if($stmt->excute()){
						return true;
						$this->length++;
					}else{
						// failed saving relation in DB. delete the newItem and drop it from items.
						$this->items[$lastAddItem]->delete();//delete DB record
						$this->dropItem($lastAddItem);//drop item
						$lastAddItem--;//previous index
						return false;
					}
      
				}else{
					return false;
				}
			}else{
				return false;
			}//end if id is null
		}//end addNewItem

		public function dropItem($index){
			//delete the record in table:list_items
			$query = 'DELETE FROM '.$this->listItems_table.
									'WHERE item_id = :item_id)';

			// Prepare statement 
			$stmt = $this->connection->prepare($query);

			// Bind parameters
			$stmt->bindParam(':item_id', $this->items[$index]->id);
			
			// Execute the statement. Check if it success inserting record. 
			if($stmt->execute()){
				unset($this->items[$index]);
				return true;
			}else{
				echo "Error:".$stmt->error."<br/>";
				return false;
			}
		}

		// Read BY ID (ver. call Item.read() for each item)
		public function read(){
			// Read head
			$query = 'SELECT head_id FROM '.$this->table.'WHERE id = ?';
			$stmt = $this->connection->prepare($query);
			$stmt = execute([$this->id]);
			$row = $stmt->fetch(PDO::FETCH_NUM);
			$newHead->id = $row[0];
			$this->head = $newHead;
			
			// Read items
			// SQL query: find the items id from list_items table
			$query = 'SELECT item_id , ordinal_num FROM '.$this->listItems_table.' WHERE id = ? ';
			$stmt = $this->connection->prepare($query);// Prepare statement 
			$stmt->bindParam(1, $this->id);// Bind ? to ID
			$stmt->execute();// Execute the statement to get the result of the query
			
			//fetch the record store in associate array and generate each item
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$newItem = new Item($this->connection);
				$newItem->id = $row['item_id']; 
				if($newItem->read()){
					$this->items[$row['ordinal_num']] = $newItem;
				}else{
					echo "failed reading item!";
				}
			}
		}//end read
		
		// Update head
		// Update item
		// Update items

		// Delete list

	}//end Class BasicList
?>
