<?php
	include_once 'ItemX.php';
	
	abstract class Base{
		// Database
		protected $connection;
		
		// Properties
		public $id;
		private const TYPE = 0;
		public $title;
		public $addTime;
		public $author_id;
		private const ITEM_TABLE = 'items';

		// Subitems Properties
		public $subItems;
		private const RELATION_TABLE = 'item_items';
		private const PARENT_ITEM = 'parent_id';
		private const CHILD_ITEM = 'child_id';
		private const DEFAULT_NEW = 0;

		// Connect to DB first
		public function __construct($pdoObj){
			$this->connection = $pdoObj;
		}

		// Methods
		abstract public function read();
		abstract public function create();
		abstract public function update();
		abstract public function delete();

		// SupItem Methods
		protected function readSupitemGen($relation_table, $parent_element, $child_element, $element_table){
			// Read supItem (unique)
			$query = "SELECT rel.".$parent_element.", itm.type".
								" FROM `".$relation_table."` AS rel".
								" INNER JOIN `".$element_table."` AS itm".
								" WHERE rel.".$child_element." = ?".
								" LIMIT 1";

			$stmt = $this->connection->prepare($query);
			$stmt->bindParam(1, $this->id);

			if(!$stmt->execute()){
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}

			// Fetch data
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$id_and_type = ['id'=>$row[$child_element], 'type'=>$row['type']];
			$supitem = new ItemX($this->connection, $id_and_type);
			if($supitem->read()){
				return $supitem->container;
			}else{
				return false;
			}

		}// End read Supitem
			
		// SubItem Methods

		protected function addSubItemGen($relation_table, $parent_element, $child_element, $subitem_id){
			// Add new relation
			$query = "INSERT INTO `".$relation_table."`".
								"(".$parent_element." ".$child_element.")".
								" VALUES (:parent_id :child_id)";	
			$stmt = $this->connection->prepare($query);

			$stmt->bindParam(':parent_id', $this->id);
			$stmt->bindParam(':child_id', $subitem_id);

			if($stmt->execute()){
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
		}// End add Subitem by id


		protected function addNewSubItemGen($title, $element_type){
			// Create New item
			$newItem = new ItemX($this->connection, ['type'=>$element_type]);
			$newItem->setData('title', $title);
			
			try{

				if($newItem->create()){
					if(!$this->addSubItem($newItem->id))
						throw new Exception("Failed adding subItem");
					return true;
				}else{
					throw new Exception("Failed creating new item!");
					return false;
				}

			}catch(Exception $e){
				print "檔案:".$e->getFile()."<br/>";
				print "行號".$e->getLine()."<br/>";
				print "錯誤:".$e->getMessage()."<br/>";
			}

		}// End add Subitem by id

		protected function readSubitemsGen($relation_table, $parent_element, $child_element, $element_table){
			// Read Item
			$query = "SELECT rel.".$child_element.", itm.type".
								" FROM `".$relation_table."` AS rel".
								" INNER JOIN `".$element_table."` AS itm".
								" WHERE rel.".$parent_element." = ?".
								" ORDER BY itm.title ASC";

			$stmt = $this->connection->prepare($query);
			$stmt->bindParam(1, $this->id);

			if(!$stmt->execute()){
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}

			// Fetch data of each item
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$id_and_type = ['id'=>$row[$child_element], 'type'=>$row['type']];
				$newItem = new ItemX($this->connection, $id_and_type);
				if($newItem->read()){
					$this->subItems[] = $newItem->container;
				}else{
					return false;
				}
			}

		}// End read Subitems


		protected function dropSubItemGen($relation_table, $parent_element, $child_element, $subitem_id){
			// Delete the record in relation table
			$query = "DELETE FROM ".$relation_table.
								" WHERE ".$parent_element." = :parent_item_id".
								" AND ".$child_element." = :child_item_id";
			$stmt = $this->connection->prepare($query);

			$stmt->bindParam(':parent_item_id', $this->id);
			$stmt->bindParam(':child_item_id', $subitem_id);

			if($stmt->execute()){
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}

		}// End drop subitem

		protected function readEleSubitemsId($relation_table, $parent_element, $child_element, $element_id){
			// Read Item
			$query = "SELECT rel.".$child_element.
								" FROM `".$relation_table."` AS rel".
								" WHERE rel.".$parent_element." = ?";

			$stmt = $this->connection->prepare($query);
			$stmt->bindParam(1, $element_id);

			$subItemId=[];// storage
			if(!$stmt->execute()){
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
			}else{
				// Fetch data 
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$subItemId[]=$row[$child_element];
				}
			}
			return $subItemId;
		}

		// write down all element in tree except the root(target), prepare for delete.
		protected function taversal($relation_table, $parent_element, $child_element, $element_id){
			$ids=[];
			// collect subitems ids
			$subItemsId = $this->readEleSubitemsId($relation_table, $parent_element, $child_element, $element_id);
			array_merge($ids, $subItemsId);
			// travel underneath
			foreach($subitemsId as $s_id){
				$ids = array_merge($ids,$this->taversal($relation_table, $parent_element, $child_element, $s_id));
			}
			return $ids;
		}

		protected function deleteEle($element_table, $element_id){
			
			$query = "DELETE FROM `".$element_table."` WHERE id = ?";
			$stmt = $this->connection->prepare($query);
			$stmt->bindParam(1, $element_id);

			if(!$stmt->execute()){
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
			
			return true;
		}

		protected function deleteAllGen($relation_table, $parent_element, $child_element, $child_element_table, $element_id){
			$id_list = taversal($relation_table, $parent_element, $child_element, $element_id);
			$id_list[]=$element_id;// add root to list
			foreach($id_list as $id){
				if(!$this->deleteEle($child_element_table, $id))
					return false;
			}
			return true;
		}

	}// End Base

	abstract class Ordered extends Base{
		// Preserve a specific order for subitems
		protected $order;

		protected function addSubItemGen($relation_table, $parent_element, $child_element, $subitem_id){
			$ordinal_num=0;
			// Find the largest ordinal number
			$query = "SELECT ordinal_num".
								" FROM `".$relation_table.
								" WHERE ".$parent_element." = ?".
								" ORDER BY ordinal_num DESC LIMIT 1";
			$stmt = $this->connection->prepare($query);
			if($stmt->execute([$this->id])){
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$ordinal_num = $row['ordinal_num']+1;// new item
			}

			// Add new relation
			$query = "INSERT INTO `".$relation_table."`".
								"(".$parent_element.", ".$child_element.", ordinal_num)".
								" VALUES (:parent_id :child_id, :ordinal_num)";	
			$stmt = $this->connection->prepare($query);

			$stmt->bindParam(':parent_id', $this->id);
			$stmt->bindParam(':child_id', $subitem_id);
			$stmt->bindParam(':ordinal_num', $ordinal_num);

			if($stmt->execute()){
				return true;
			}else{
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
		}// End add Subitem by id


		protected function readSubitemsGen($relation_table, $parent_element, $child_element, $element_table){
			// Read Item
			$query = "SELECT rel.".$child_element.", itm.type".
								" FROM `".$relation_table."` AS rel".
								" INNER JOIN `".$element_table."` AS itm".
								" WHERE rel.".$parent_element." = ?".
								" ORDER BY rel.ordinal_num ASC";

			$stmt = $this->connection->prepare($query);
			$stmt->bindParam(1, $this->id);

			if(!$stmt->execute()){
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}

			// Fetch data of each item
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$id_and_type = ['id'=>$row[$child_element], 'type'=>$row['type']];
				$newItem = new ItemX($this->connection, $id_and_type);
				if($newItem->read()){
					$this->subItems[] = $newItem->container;
				}else{
					return false;
				}
			}

		}// End read Subitems

		//update order
		protected function updateOrderGen($relation_table, $parent_element, $child_element, $element_table){
			if(!is_null($this->order)){
				foreach($this->order as $index=>$item_id){
					$query = "UPDATE `".$relation_table."`".
										" SET ordinal_num = :ordinal_num".
										" WHERE `".$parent_element."` = :parent_element AND ".$child_element." = :child_element";
					$stmt = $this->connection->prepare($query);
					if(!$stmt->execute(['ordinal_num'=>$index, 'parent_element'=>$this->id, 'child_element'=>$item_id])){
						print "{$stmt->error}";
						return false;
					}
					return true;
				}
			}
		}// End update order

	}

?>
