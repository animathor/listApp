<?php
	include_once 'Items.php';

	class ItemX{
		//Database
		private $connection;
		private $listItems_table = 'list_items';
		private $items_table = 'items';
		private $list_table = 'list';

		//Properties
		private $id;
		private $type;
		private $container;

		//Constructor
		public function __construct($pdoObj,$id_type_array){
			$this->connection = $pdoObj;

			// 1. 有type沒id, 生item   2.有id沒type, 查type, 生item 存id  3.id, type 都有
			if(isset($id_type_array['id'])){
				$this->id = $id_type_array['id'];
				if(isset($id_type_array['type'])){
					$this->type = $id_type_array['type'];
				}else{
					$this->type = $this->fetch_type($this->id);
				}
				$this->container = $this->distributeContainer($this->type);
				$this->container->id = $this->id;
			}else{
				$this->container = $this->distributeContainer($this->type);
			}
		}//end Constructor

		public function distributeContainer(int $item_type){
			switch($item_type){
				case 0:
					$item = new Item($this->connection);
					return $item;
				case 1:
					$item = new Check($this->connection);
					return $item;
				case 2:
					$item = new Task($this->connection);
					return $item;
				default:
					return null;
			}
		}

		public function fetch_type($item_id){
			$query = 'SELECT FROM '.$this->listItems_table.' WHERE item_id = ?';
			$stmt = $this->connection->prepare($query);
			if($stmt->execute([$item_id]) && $row = $stm->fetch(PDO::FETCH_NUM))
				return $row[0];
			else
				return null;
		}

		public function read(){
			$this->container->read();
			return $this>container;
		}

		public function create(){
			return $this->container->create();
		}

		public function delete(){
			return $this->container->delete();
		}
		
		public function copy(){
			if($this->container->read() && $this->container->create())
				return $this->container;
		}
		
		public function schedule(){
			if($this->type === 2){
				return $this->container->schedule;
			}
			return false;
		}

		public function update(){
			return $this->container->update();
		}

		public function setData($prop_name,$value){
			// item
			switch($this->type){
				case 0:
					if($prop_name == 'title'){
						$this->container->title = $value;
						return true;
					}elseif($prop_name == 'note'){
						$this->container->note = $value;
						return true;
					}else{
						return false;						 
					}
				case 1:
					if($prop_name == 'title'){
						$this->container->title = $value;
						return true;
					}elseif($prop_name == 'note'){
						$this->container->note = $value;
						return true;
					}elseif($prop_name == 'checked'){
						$this->container->checked= $value;
						return true;
					}else{
						return false;						 
					}
				case 2:
					if($prop_name == 'title'){
						$this->container->title = $value;
						return true;
					}elseif($prop_name == 'note'){
						$this->container->note = $value;
						return true;
					}elseif($prop_name == 'checked'){
						$this->container->checked= $value;
						return true;
					}elseif($prop_name == 'schedule'){
						$this->container->schedule= $value;
						return true;
					}elseif($prop_name == 'due'){
						$this->container->due= $value;
						return true;
					}elseif($prop_name == 'startTime'){
						$this->container->startTime= $value;
						return true;
					}elseif($prop_name == 'endTime'){
						$this->container->endTime= $value;
						return true;
					}else{
						return false;
					}
				}//end switch
		}//end setItem
		
		public function check($on_off){
			return $this->container->check($on_off);
		}
	}// end class ItemX
?>
