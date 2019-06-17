<?php
include'Items.php';
	class GeneralList {
		//DB
		private $connection;
		private $table = 'lists';
		private $listItems_table = 'list_items';
		private $items_table = 'items';

		//List properties
		public $id;
		public $head;
		public $items;
		public $length=0;
		public $listType;//(0:item, 1:check, 2:task)
		private $headTypeTable = [0,1,2];//(list type, head type)[(item,item),(check, check), (task, task)
		private $newItemTypeTable = [0,1,2];//(list type, items type)[(item,item),(check, check), (task, task)
		private $lastAddIndex=0;

		//Constructor
		public function __construct($pdoObj, $listType){
			$this->connection = $pdoObj;
			$this->listType = $listType;
		}
		
		protected distributeContainer($itemType){
			switch($itemType){
				case:0
					$item = new Item($this->connection);
					return $item;
				case:1
					$item = new Check($this->connection);
					return $item;
				case:2
					$item = new Task($this->connection);
					return $item;
			}
		}

		// Create empty list 
		public function create($listTitle){
			//根據 list 類型 查表其 head 類型，在選定裝的物件類型
			$newHead = $this->distributeContainer($headTypeTable[$this->listType]);//head type follows list type 
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
				// Create item in DB, get id.;
													//根據 list 類型 查表其 new item 類型，從而選定裝的物件類型
				$newItem = $this->distributeContainer($this->$newItemTypeTable[$this->listType])
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
			// Read head and its type to generate list
			$query = 'SELECT head_id, type FROM '.$this->table.
								'WHERE id = ? LIMIT 0,1';
			$stmt = $this->connection->prepare($query);
			$stmt = execute([$this->id]);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$newHead = $this->distributeContainer($this->$headTypeTable[$row['type']]);//根據 list 類型 查表其 head 類型，在選定裝的物件類型
			$newHead->id = $row['head_id'];
			$this->head = $newHead;
			
			// Read items
			// SQL query: find the items id from list_items table and list type from items
			$query = 'SELECT l_i.item_id , l_i.ordinal_num, i.type as item_type FROM '.$this->listItems_table.'AS l_i'.
								'INNER JOIN '.$items_table.'AS i ON l_i.item_id = i.id'.
								' WHERE l_i.list_id = ? ';
			$stmt = $this->connection->prepare($query);// Prepare statement 
			$stmt->bindParam(1, $this->id);// Bind ? to ID
			$stmt->execute();// Execute the statement to get the result of the query
			
			//fetch the record store in associate array and generate each item
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$newItem = $this->distributeContainer($row['item_type']);//根據 item type 選定要裝的物件類別
				$newItem->id = $row['item_id']; 
				if($newItem->read()){
					$this->items[$row['ordinal_num']] = $newItem;
				}else{
					echo "failed reading item!";
				}
			}
		}//end read
		
		// Update head
		public updateHead(){
			
		}
		// Update item
		// Update items

		// Delete list

	}//end Class BasicList
?>
