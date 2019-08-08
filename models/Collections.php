<?php
	class Collection extends Base{

		// Properties
		private const TYPE = 1;
		private const COLLECTION_TABLE ='collections';
		private const ITEM_TABLE ='items';

		// Subcollections Properties
		private const COLLECTION_COLLECTIONS='collection_collections';
		private const PARENT_COLLECTION = 'parent_collection_id';
		private const CHILD_COLLECTION = 'child_collection_id';
		private const DEFAULT_NEW = 1;// add new collection

		// Subitems Properties
		private const COLLECTION_ITEMS='collection_items';
		private const CHILD_ITEM= 'child_item_id';

		// Methods
		public function read(){
			$query = 'SELECT type, title, addTime FROM '.self::COLLECTION_TABLE.' WHERE id = ? ';
			$stmt = $this->connection->prepare($query);

			$stmt->bindParam(1,$this->id);

			if($stmt->execute()){
				//fetch the record store in associate array
				$row = $stmt->fetch(PDO::FETCH_ASSOC);

				//assign each property the value in the corresponding field
				$this->type = $row['type'];
				$this->title = $row['title'];
				$this->addTime = $row['addTime'];
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}

		}

		public function creat(){
			$query = 'INSERT INTO '.self::COLLECTION_TABLE.'(type, title)'.
								'VALUES(:type, :title)';
			$stmt = $this->connection->prepare($query);

			$this->title = htmlspecialchars(strip_tags($this->title));

			$stmt->bindParam(':type', self::TYPE);
			$stmt->bindParam(':title', $this->title);

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

		public function update(){
			$query = 'UPDATE FROM '.self::COLLECTION_TABLE.
								'SET title = :title'.
								'WHERE id = :id';
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
							return false;
						}
					}
					// delete collection element
					if(!deleteEle(self::COLLECTION_TABLE, $collection_id)){
							throw new Exception("Collection $collection_id not deleted");
							return false;
					}
				}// finish all collections
				return true;
				
			}catch(Exception $e){
				print "檔案:".$e->getFile()."<br/>";
				print "行號".$e->getLine()."<br/>";
				print "錯誤:".$e->getMessage()."<br/>";
			}

		}//end delete


		// Subitems Methods

		public function addList($item_id){
			return $this->addSubItemGen(self::COLLECTION_ITEMS, self::PARENT_COLLECTION, self::CHILD_ITEM, $item_id);
		}

		public function addNewList($item_type, $title){
			return $this->addNewSubItemGen($item_type, $title);
		}

			// Read and store in subitems[]
		public function readLists(){
			return $this->readSubitemsGen(self::COLLECTION_ITEMS, self::PARENT_COLLECTION, self::CHILD_ITEM, self::ITEMS_TABLE);
		}

		public function dropList($item_id){
			return $this->dropSubItemGen(self::COLLECTION_ITEMS, self::PARENT_COLLECTION, self::CHILD_ITEM, $item_id);
		}

		public function deleteList($item_id){
			return $this->deleteAllGen(self::ITEM_ITEMS, self::PARENT_ITEM, self::CHILD_ITEM, $item_id);
		}

		// Subcollections Methods

		public function addSubCollection($subcollection_id){
			return $this->addSubItemGen(self::COLLECTION_COLLECTIONS, self::PARENT_COLLECTION, self::CHILD_COLLECTION, $subcollection_id);
		}

		public function addNewSubCollection($title){
			return $this->addNewSubItemGen(self::DEFAULT_NEW, $title);
		}

			//	 Read and store in subitems[]
		public function readSubCollections(){
			try{
				// Read collection
				if(!$this->readSubitemsGen(self::COLLECTION_COLLECTIONS, self::PARENT_COLLECTION, self::CHILD_COLLECTION, self::COLLECTIONS_TABLE))
					throw new Exception('Reading subcollections failed!');

				// Read list
				return $this->readList();
			}catch(Exception $e){
				print "檔案:".$e->getFile()."<br/>";
				print "行號".$e->getLine()."<br/>";
				print "錯誤:".$e->getMessage()."<br/>";
			}

		}

		public function dropSubCollection($collection_id){
			return $this->dropSubItemGen(self::COLLECTION_COLLECTIONS, self::PARENT_COLLECTION, self::CHILD_COLLECTION, $collection_id);
		}
		
	}// End Collection
	
?>
