<?php
	include 'Items.php';
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
		public $order;//取得 item index 排列順序 (original index => new index)
		public $listType;//(0:item, 1:check, 2:task)
		private $headTypeTable = [0,1,2];//(list type, head type)[(item,item),(check, check), (task, task)
		private $newItemTypeTable = [0,1,2];//(list type, items type)[(item,item),(check, check), (task, task)
		private $lastAddIndex=0;

		//Constructor
		public function __construct(PDO $pdoObj, int $listType){
			$this->connection = $pdoObj;
			$this->listType = $listType;
		}
		
		protected distributeContainer(int $itemType){
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
		public function create(string $listTitle){
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

		protected function addItemToList(Item $item){
			//store in items[]
			$this->items[] = $item;
			//update lastAddIndex
			//QQ_為了取得 item 的 index 可以用 key(end($items)) , array_search($newItem, $items) 來取得，最後決定單純一點用var存lastAddIndex
			if($this->lastAddIndex >0)
				$lastAddIndex++;
      
			// Add new list relation
			$query = 'INSERT INTO '.$this->table.
								'SET list_id = :list_id, item_id = :item_id, item_ordinalNum = :item_ordinalNum';
			
			// Prepare statement
			$stmt = $this->connection->prepare($query);
    
			// Bind Parameters
			$stmt->bindParam(':list_id', $this->id);
			$stmt->bindParam(':item_id', $item->id);//QQ_ same as $this->items[$lastAddItem]->id but...
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
		}//end add item to list
		
		public function addNewItem($title){
			// you can't add item before you have the list(record in DB)QQ_neccessary?
			if(!is_null($this->id)){
				// Create item in DB, get id.;
													//根據 list 類型 查表其 new item 類型，從而選定裝的物件類型
				$newItem = $this->distributeContainer($this->$newItemTypeTable[$this->listType])
				$newItem->title = $title;
				if($newItem->create()){
      		return addItemToList($newItem);
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
		
		// Update List
		public function update(){
			// update head
			$this->head->update();

			if(!is_null($this->order)){
				// update list-items ordinal_num
				foreach($this->order as $original_i=>$new_i){
					$query = 'UPDATE FROM'.$this->listItems_table.' SET ordinal_num = :new_i WHERE id = :list_id AND ordinal_num = :original_i';
					$stmt = $this->connection->prepare($query);
					$stmt->execute(['new_i'=>$new_i, 'original_i'=>$original_i, 'list_id'=>$this->id]);
				}
			}

			// update each item
			foreach($this->items as $item){
				$item->update();
			}
		

		// Update item by index
		public function updateItem($index){
			return $this->items[$index]->update();

		}

		// Check List
		public function check(){
			// if it is a check list
			if($this->listType == 1 || $this->listType == 2){
				$this->head->check();
				foreach($this->items as $item){
					if(!$item->check())
						return false;
				}
				return true;
			}
			else
				return false;
		}

		// Check item
		public function checkitem($index){
			if($this->listType == 1 || $this->listType == 2){
				return $this->item[$index]->check();
			}
			else
				return false;
		}

		// Schedule List head
		public schedule(){
			if($this->listType == 2){
				return $this->head->schedule();
			}else{
				return false;
			}
		}

		// Schedule item
		public scheduleItem($index){
			if($this->listType == 2){
				return $this->items[$index]->schedule();
			}else{
				return false;
			}
		}

		// Delete list
		public function delete(){
			//delete head
			if(!$this->head->delete())
				return false;

			//delete items
			foreach($this->items as $item){
				if(!$item->delete())
					return false;
			}
			return true;
		}//end delete list

		// Delete item
		public function deleteItem(int $index){
			if($this->items[$index]->delete()){
				$length--;
				return true;
			}else{
				return false;
			}
		}

		// Copy item
		public function copyItem(int $index){
			$copy = clone $this->items[$index];
			$copy->id = null;
			return $copy;
		}

		// Cut item
		public function cutItem(int $index){
			$item = $this->items[$index];//get the item
			$this->dropItem($index);//drop item from list
			return $item;
		}
		
		// Paste item to list
		public function includeItem(Item $item){
			// Recieve a copy
			if(is_null($item->id)){
				if($item->create()){
					return addItemToList($item);
				}else{
					return false;
				}
			}
			// Recieve a item
			else{
				return addItemToList($item);
			}
		}
		
		// Add another list as sublist. Or to say including head item of list
		public function includeSublist(GeneralList $sublist){
			if($sublist->head->type === $itemTypeTable[$this->id]){
				return addItemToList($sublist->head);
			}else{
			//QQ_change item type could wait...
				return null;
			}
		}

		public function createSublist($index){
			// sublist type is same as item type
			$sublistType = $this->items[$index]->type;
			$head = $this->items[$index]->head;

			// Create list
			$sublist = new GeneralList($this->connection, $sublistType);
			$query = 'INSERT INTO '.this->table.'(head_id, type) VALUES(:head_id, :listType)';
			$stmt = $this->connection->prepare($query);
			$stmt->bindParam(':head_id',$head->id);
			$stmt->bindParam(':listType',$sublistType);
			if($stmt->execute()){
				//get list id
				$result = $this->connection->query('SELECT LAST_INSERT_ID()', PDO::FETCH_NUM);//useing num since it only has one field
				$this->id = $result[0];//store lists id
				$sublist->head = $head;
				return $sublist;
			}else{
				return null;//QQ_null?
			}
		}

	}//end Class BasicList
?>
