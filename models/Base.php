<?php
	include_once 'ItemX.php';
	
	abstract class Base{
		// Database
		protected $connection;
		
		// Properties
		public $id;
		public $type = 0;
		public $title;
		public $addTime;
		public $author_id;
		private const ITEM_TABLE = 'items';

		// Subitems Properties
		public $subItems;
		private const RELATION_TABLE = 'item_items';
		private const PARENT_ITEM = 'parent_item';
		private const CHILD_ITEM = 'child_item';
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
		
		protected function readSupEle($relation_table, $parent_element, $child_element, $element_id,$element_type){
			// Read supItem (unique)
			$query = "SELECT rel.".$parent_element.
								" FROM `".$relation_table."` AS rel".
								" WHERE rel.".$child_element." = ?".
								" LIMIT 1";
			try{
				$stmt = $this->connection->prepare($query);
				$stmt->bindParam(1, $element_id);
				$stmt->execute();
				// Fetch data
				if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$id_and_type = ['id'=>$row[$parent_element], 'type'=>$element_type];
					$itemx = new ItemX($this->connection, $id_and_type);
					if($itemx->read()){
						return $itemx->container;
					}else{
						return false;
					}
				}
				return false;
			}catch(Exception $e){
				return false;
			}

		}// End read VSupEle
		
		protected function readVSupEle($relation_table, $parent_element, $child_element, $element_table, $element_id){
			// Read supItem (unique)
			$query = "SELECT rel.".$parent_element.", itm.type".
								" FROM `".$relation_table."` AS rel".
								" INNER JOIN `".$element_table."` AS itm ON rel.".$parent_element."= itm.id".
								" WHERE rel.".$child_element." = ?".
								" LIMIT 1";
			try{
				$stmt = $this->connection->prepare($query);
				$stmt->bindParam(1, $element_id);
				$stmt->execute();
				// Fetch data
				if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$id_and_type = ['id'=>$row[$parent_element], 'type'=>$row['type']];
					$item = new ItemX($this->connection, $id_and_type);
					if($item->read()){
						return $item->container;
					}else{
						return false;
					}
				}
				return false;
			}catch(Exception $e){
				return false;
			}

		}// End read VSupEle
		
		protected function traceBackVEle($relation_table, $parent_element, $child_element, $element_table, $element_id){
			$item_train=[];
			$supitem=$this->readVSupEle($relation_table, $parent_element, $child_element, $element_table, $element_id);
					
			while($supitem){
				$item_train[]=$supitem;
				// next supitem
				$supitem = $this->readVSupEle($relation_table, $parent_element, $child_element, $element_table , $supitem->id);
			}
			return $item_train;
		}
		
		protected function traceBackEle($relation_table, $parent_element, $child_element, $element_id,$element_type){
			$item_train=[];
			$supitem=$this->readSupEle($relation_table, $parent_element, $child_element, $element_id,$element_type);
			while($supitem){
				$item_train[]=$supitem;
				// next supitem
				$supitem = $this->readSupEle($relation_table, $parent_element, $child_element, $supitem->id,$element_type);
			}
			return $item_train;
		}
		
		// SubItem Methods

		protected function addSubItemGen($relation_table, $parent_element, $child_element, $subitem_id){
			// Add new relation
			$query = "INSERT INTO `".$relation_table."`".
								"(".$parent_element.", ".$child_element.")".
								" VALUES (:parent_id, :child_id)";	
			try{
				$stmt = $this->connection->prepare($query);

				$stmt->bindParam(':parent_id', $this->id);
				$stmt->bindParam(':child_id', $subitem_id);

				$stmt->execute();
				return true;
			}catch(Exception $e){
				return false;
			}
		}// End add Subitem by id


		protected function addNewSubItemGen($relation_table, $parent_element, $child_element, $title, $element_type= self::DEFAULT_NEW, $author_id){
			// Create New item
			$id_n_type = ['type'=>$element_type];
			try{
				$newItem = new ItemX($this->connection, $id_n_type);
				$newItem->setData('title', $title);
				$newItem->setData('author_id', $author_id); 
				if($newItem->create()){
					if(!$this->addSubItemGen($relation_table, $parent_element, $child_element, $newItem->id))
						throw new Exception("Failed adding subItem =".$newItem->id);
					return $newItem->id;
				}else{
					throw new Exception("Failed creating new item!");
				}

			}catch(Exception $e){
					return false;
			}

		}// End add Subitem by id

		protected function readSubitemsGen($relation_table, $parent_element, $child_element, $element_table){
			// Read Item
			$query = "SELECT rel.".$child_element.", itm.type".
								" FROM `".$relation_table."` AS rel".
								" INNER JOIN `".$element_table."` AS itm ON rel.".$child_element."= itm.id".
								" WHERE rel.".$parent_element." = ?".
								" ORDER BY itm.type ASC, itm.title ASC";

			try{
				$stmt = $this->connection->prepare($query);
				$stmt->bindParam(1, $this->id);

				$stmt->execute();
			
				// Fetch data of each item
				$subItems = [];
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$id_and_type = ['id'=>$row[$child_element], 'type'=>$row['type']];
					$newItem = new ItemX($this->connection, $id_and_type);
					if(!$newItem->read()){
						throw new Exception("fail to read subitem");
					}
						$subItems[] = $newItem->container;
				}
				return $subItems;
			}catch(Exception $e){
				return false;
			}

		}// End read Subitems


		protected function dropSubItemGen($relation_table, $parent_element, $child_element, $subitem_id){
			// Delete the record in relation table
			$query = "DELETE FROM ".$relation_table.
								" WHERE ".$parent_element." = :parent_item_id".
								" AND ".$child_element." = :child_item_id";
			try{
				$stmt = $this->connection->prepare($query);
  
				$stmt->bindParam(':parent_item_id', $this->id);
				$stmt->bindParam(':child_item_id', $subitem_id);

				$stmt->execute();
				return true;
			}catch(Exception $e){
				return false;
			}

		}// End drop subitem

		protected function readEleSubitemsId($relation_table, $parent_element, $child_element, $element_id){
			// Read Item
			$query = "SELECT rel.".$child_element.
								" FROM `".$relation_table."` AS rel".
								" WHERE rel.".$parent_element." = ?";

			try{
				$stmt = $this->connection->prepare($query);
				$stmt->bindParam(1, $element_id);

				$subItemId=[];// storage
				$stmt->execute();
				// Fetch data 
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$subItemId[]=$row[$child_element];
				}
			}catch(Exception $e){
			}
				return $subItemId;
		}
		

		// write down all element in tree except the root(target), prepare for delete.
		protected function traversal($relation_table, $parent_element, $child_element, $element_id){
			$ids=[];
			// collect subitems ids
			$subItemsId = $this->readEleSubitemsId($relation_table, $parent_element, $child_element, $element_id);
			array_merge($ids, $subItemsId);
			// travel underneath
			foreach($subItemsId as $s_id){
				$ids = array_merge($ids,$this->traversal($relation_table, $parent_element, $child_element, $s_id));
			}
			return $ids;
		}

		protected function deleteEle($element_table, $element_id){
			$query = "DELETE FROM `".$element_table."` WHERE id = ?";
			try{
				$stmt = $this->connection->prepare($query);
				$stmt->bindParam(1, $element_id);

				$stmt->execute();
				return true;
			}catch(Exception $e){
				return false;
			}
		}

		protected function deleteAllGen($relation_table, $parent_element, $child_element, $child_element_table, $element_id){
			$id_list = $this->traversal($relation_table, $parent_element, $child_element, $element_id);
			$id_list[]=$element_id;// add root to list
			foreach($id_list as $id){
				if(!$this->deleteEle($child_element_table, $id))
					return false;
			}
			return true;
		}
		protected function moveSubELeGen($relation_table, $parent_element, $child_element, $former_parent, $dragged_child){
			try{
				// start transaction for update all item ordinal num
				$this->connection->beginTransaction();
				// change parent
				$query = "UPDATE `".$relation_table."`".
									" SET ".$parent_element." = :new_parent".
									" WHERE `".$parent_element."` = :former_parent AND ".$child_element." = :dragged_child";
				$stmt = $this->connection->prepare($query);
				$stmt->bindParam(':former_parent',$former_parent);
				$stmt->bindParam(':new_parent',$this->id);
				$stmt->bindParam(':dragged_child',$dragged_child);
				$stmt->execute();
				
				$this->connection->commit();
				return true;
			}catch(Exception $e){
				$this->connection->rollBack();
				return false;
			}
		}// end move
	}// End Base

	abstract class Ordered extends Base{
		// Preserve a specific order for subitems
		public $order;

		protected function addSubItemGen($relation_table, $parent_element, $child_element, $subitem_id){
			$ordinal_num=0;
			// Find the largest ordinal number
			$query = "SELECT ordinal_num".
								" FROM ".$relation_table.
								" WHERE ".$parent_element." = ?".
								" ORDER BY ordinal_num DESC LIMIT 1";
			try{
				$stmt = $this->connection->prepare($query);
				$stmt->execute([$this->id]);
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if($row){
					$ordinal_num = $row['ordinal_num']+1;// new item
				}
			

				// Add new relation
				$query = "INSERT INTO `".$relation_table."`".
								"(".$parent_element.", ".$child_element.", ordinal_num)".
								" VALUES (:parent_id, :child_id, :ordinal_num)";
				$stmt = $this->connection->prepare($query);

				$stmt->bindParam(':parent_id', $this->id);
				$stmt->bindParam(':child_id', $subitem_id);
				$stmt->bindParam(':ordinal_num', $ordinal_num);

				$stmt->execute();
				return true;
			}catch(Exception $e){
				return false;
			}
		}// End add Subitem by id


		protected function readSubitemsGen($relation_table, $parent_element, $child_element, $element_table){
			
			// Read Item
				$query = "SELECT rel.".$child_element.", itm.type".
								" FROM `".$relation_table."` AS rel".
								" INNER JOIN `".$element_table."` AS itm ON rel.".$child_element."= itm.id".
								" WHERE rel.".$parent_element." = ?".
								" ORDER BY rel.ordinal_num ASC";
			try{
				$stmt = $this->connection->prepare($query);
				$stmt->bindParam(1, $this->id);
	
				$stmt->execute();
			
				// Fetch data of each item
				$subItems = [];
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$id_and_type = ['id'=>$row[$child_element], 'type'=>$row['type']];
					$newItem = new ItemX($this->connection, $id_and_type);

					if(!$newItem->read()){
						throw new Exception('fail to read subitem');
					}
					$subItems[] = $newItem->container;
				}
				return $subItems;
				
			}catch(Exception $e){
				return false;
			}

		}// End read Subitems

		//update order
		public function updateOrderGen($relation_table, $parent_element, $child_element){
			try{
				// start transaction for update all item ordinal num
				$this->connection->beginTransaction();
				$query = "UPDATE `".$relation_table."`".
									" SET ordinal_num = :ordinal_num".
									" WHERE `".$parent_element."` = :parent_element AND ".$child_element." = :child_element";
				$stmt = $this->connection->prepare($query);
				$ordinal_num = $child_id = null;
				$stmt->bindParam(':parent_element',$this->id);
				$stmt->bindParam(':ordinal_num', $ordinal_num);
				$stmt->bindParam(':child_element',$child_id);
				foreach($this->order as $ordinal_num=>$child_id){
					$stmt->execute();
				}
				$this->connection->commit();
				return true;
			}catch(Exception $e){
				$this->connection->rollBack();
				return false;
			}
		}// End update order
		
		protected function moveSubELeGen($relation_table, $parent_element, $child_element, $former_parent, $dragged_child){
			try{
				// start transaction for update all item ordinal num
				$this->connection->beginTransaction();
				// change parent
				$query = "UPDATE `".$relation_table."`".
									" SET ".$parent_element." = :new_parent".
									" WHERE `".$parent_element."` = :former_parent AND ".$child_element." = :dragged_child";
				$stmt = $this->connection->prepare($query);
				$stmt->bindParam(':former_parent',$former_parent);
				$stmt->bindParam(':new_parent',$this->id);
				$stmt->bindParam(':dragged_child',$dragged_child);
				$stmt->execute();

				//update order
				$query = "UPDATE `".$relation_table."`".
									" SET ordinal_num = :ordinal_num".
									" WHERE `".$parent_element."` = :parent_element AND ".$child_element." = :child_element";
				$stmt = $this->connection->prepare($query);
				$ordinal_num = $child_id = null;
				$stmt->bindParam(':parent_element',$this->id);
				$stmt->bindParam(':ordinal_num', $ordinal_num);
				$stmt->bindParam(':child_element',$child_id);
				foreach($this->order as $ordinal_num=>$child_id){
					$stmt->execute();
				}
				$this->connection->commit();
				return true;
			}catch(Exception $e){
				$this->connection->rollBack();
				return false;
			}
		}// end move
	}

?>
