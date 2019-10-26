<?php
	include_once 'Items_obj.php';
	
	class Collection extends Base{

		// Properties
		public $type = 1;
		private const COLLECTIONS_TABLE ='collections';
		private const ITEMS_TABLE ='items';

		// Subcollections Properties
		public $subCollections;
		private const COLLECTION_COLLECTIONS='collection_collections';
		private const PARENT_COLLECTION = 'parent_collection';
		private const CHILD_COLLECTION = 'child_collection';
		private const DEFAULT_NEW = 1;// add new collection

		// Subitems(list) Properties
		private const COLLECTION_ITEMS='collection_items';
		private const PARENT_ITEM= 'parent_item';
		private const CHILD_ITEM= 'child_item';
		private const ITEM_ITEMS='item_items';
		// Methods
		public function read(){
			$query = 'SELECT title, addTime, author_id FROM '.self::COLLECTIONS_TABLE.' WHERE id = ? ';
			$stmt = $this->connection->prepare($query);

			$stmt->bindParam(1,$this->id);

			if($stmt->execute()){
				//fetch the record store in associate array
				$row = $stmt->fetch(PDO::FETCH_ASSOC);

				//assign each property the value in the corresponding field
				$this->title = $row['title'];
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
			$query = 'INSERT INTO '.self::COLLECTIONS_TABLE.'(title, author_id)'.
								'VALUES(:title, :author_id)';
			$stmt = $this->connection->prepare($query);

			$this->title = htmlspecialchars(strip_tags($this->title));

			//$stmt->bindParam(':type', self::TYPE);
			$stmt->bindParam(':title', $this->title);
			$stmt->bindParam(':author_id', $this->author_id);

			if($stmt->execute()){
				//get id
			
					//$stmt = $this->connection->query('SELECT LAST_INSERT_ID()');
					//$result = $stmt->fetch(PDO::FETCH_NUM);
					//$this->id = $result[0];		
				//one line using pdo method instead
				$this->id = $this->connection->lastInsertId();
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}

		}

		public function update(){
			$query = 'UPDATE '.self::COLLECTIONS_TABLE.' SET title = :title WHERE id = :id';
			$stmt = $this->connection->prepare($query);
			// Clean data
			$this->title = htmlspecialchars(strip_tags($this->title));
			//$this->author_id = htmlspecialchars(strip_tags($this->author_id));

			// Bind parameters
			$stmt->bindParam(':title', $this->title);
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
			try{
				// Find all collection
				$collections = $this->traversal(self::COLLECTION_COLLECTIONS, self::PARENT_COLLECTION, self::CHILD_COLLECTION, $this->id);
				$collections[] = $this->id;// include current collection
      
				// delete	all list in each collection
				foreach($collections as $collection_id){
					// read lists in the collection
					$lists = $this->readEleSubitemsId(self::COLLECTION_ITEMS, self::PARENT_COLLECTION, self::CHILD_ITEM, $collection_id);
					// and delete them
					foreach($lists as $list_id){
						if(!$this->deleteList($list_id)){
							throw new Exception("List $list_id not deleted");
						}
					}
					// delete collection element
					if(!$this->deleteEle(self::COLLECTIONS_TABLE, $collection_id)){
							throw new Exception("Collection $collection_id not deleted");
					}
				}// finish all collections
				return true;
				
			}catch(Exception $e){
				return false;
			}

		}//end delete
				
		// Supitem Methods
		
		
		
		public function traceBack(){
			return $this->traceBackEle(self::COLLECTION_COLLECTIONS, self::PARENT_COLLECTION, self::CHILD_COLLECTION, $this->id, $this->type);
		}

		// Subitems Methods

		public function addList($item_id,$author_id){
			return $this->addSubItemGen(self::COLLECTION_ITEMS, self::PARENT_COLLECTION, self::CHILD_ITEM, $item_id);
		}

		public function addNewList($title,$item_type,$author_id){
			return $this->addNewSubItemGen(self::COLLECTION_ITEMS, self::PARENT_COLLECTION, self::CHILD_ITEM, $title, $item_type,$author_id);// return list id to working on the webpage. Otherwise, return false.
		}

			// Read and store in subitems[]
		public function readLists(){
			$subItems = $this->readSubitemsGen(self::COLLECTION_ITEMS, self::PARENT_COLLECTION, self::CHILD_ITEM, self::ITEMS_TABLE);
			if($subItems === false){
				return false;
			}else{
				$this->subItems = $subItems;
				return true;
			}
		}

		public function dropList($item_id){
			return $this->dropSubItemGen(self::COLLECTION_ITEMS, self::PARENT_COLLECTION, self::CHILD_ITEM, $item_id);
		}

		public function deleteList($item_id){
			return $this->deleteAllGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM,self::ITEMS_TABLE, $item_id);
		}

		// Subcollections Methods

		public function addSubCollection($subcollection_id,$author_id){
			return $this->addSubItemGen(self::COLLECTION_COLLECTIONS, self::PARENT_COLLECTION, self::CHILD_COLLECTION, $subcollection_id);
		}

		public function addNewSubCollection($title, $author_id){
			return $this->addNewSubItemGen(self::COLLECTION_COLLECTIONS, self::PARENT_COLLECTION, self::CHILD_COLLECTION,$title,self::DEFAULT_NEW, $author_id);
		}

		protected function readSubCollections(){
			// Read
			$query = "SELECT ".self::CHILD_COLLECTION.
								" FROM `".self::COLLECTION_COLLECTIONS."` AS crc".
								" INNER JOIN ".self::COLLECTIONS_TABLE." AS c ON crc.".self::CHILD_COLLECTION." = c.id".
								" WHERE ".self::PARENT_COLLECTION." = ?".
								" ORDER BY title ASC";

			$stmt = $this->connection->prepare($query);
			$stmt->bindParam(1, $this->id);

			if(!$stmt->execute()){
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}

			// Fetch data of each item
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$newCollection = new Collection($this->connection);
				$newCollection->id = $row[self::CHILD_COLLECTION];
				if($newCollection->read()){
					$this->subCollections[] = $newCollection;
				}else{
					return false;
				}
			}
			return true;

		}// End read Subitems
			//	 Read and store in subitems[]
		public function readAllSub(){
			try{
				// Read collection
				if(!$this->readSubCollections()){
					throw new Exception('Reading subcollections failed!');
					
					}

				// Read list
				if(!$this->readLists()){
					throw new Exception('Reading subLists failed!');
				}
				return true;
				
			}catch(Exception $e){
				throw $e;
				return false;
			}

		}

		public function dropSubCollection($collection_id){
			return $this->dropSubItemGen(self::COLLECTION_COLLECTIONS, self::PARENT_COLLECTION, self::CHILD_COLLECTION, $collection_id);
		}
		
	}// End Collection
	
?>
