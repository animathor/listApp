<?php
	include_once 'Items_obj.php';
	include_once 'Collections.php';

	class ItemX{
		//Database
		private $connection;
		protected	const ITEMS_TABLE = 'items';

		//Properties
		public $id;
		public $type;
		public $container;

		//Constructor
		public function __construct($pdoObj,$id_type_array){
			$this->connection = $pdoObj;

			// 1.id, type 都有   2.有id沒type, 查type, 生item 存id  3. 有type沒id, 生item
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
				$this->type = $id_type_array['type'];
				$this->container = $this->distributeContainer($this->type);
			}
		}//end Constructor

		public function distributeContainer(int $item_type){
			switch($item_type){
				case 1:
					$item = new Collection($this->connection);
					return $item;
				case 2:
					$item = new Item($this->connection);
					return $item;
				case 4:
					$item = new Check($this->connection);
					return $item;
				case 6:
					$item = new Task($this->connection);
					return $item;
				default:
					return null;
			}
		}

		public function fetch_type($item_id){
			$query = 'SELECT FROM '.self::ITEMS_TABLE.' WHERE id = ?';
			$stmt = $this->connection->prepare($query);
			if($stmt->execute([$item_id]) && $row = $stm->fetch(PDO::FETCH_NUM))
				return $row[0];
			else
				return null;
		}

		public function read(){
			return $this->container->read();
		}

		public function create(){
			if($this->container->create()){
				$this->id = $this->container->id;
				return true;
			}else{
				return false;
			}
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
				case 1:
					if($prop_name == 'title'){
						$this->container->title = $value;
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
					}else{
						return false;						 
					}
				case 4:
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
				case 6:
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
					}elseif($prop_name == 'timer'){
						$this->container->timer= $value;
						return true;
					}elseif($prop_name == 'totalTime'){
						$this->container->totalTime= $value;
						return true;
					}else{
						return false;
					}
				}//end switch
		}//end setItem
		
		public function check($on_off){
			return $this->container->check($on_off);
		}
		
		// Subitem methods
		public function addSubitem($item_id){
			return $this->container->addSubitem($item_id);
		}

		public function addNewSubitem($title){
			return $this->container->addNewSubitem($title);// dynamic type is not allow. Maybe add a selector for user.
		}

			// Read and store in subitems[]
		public function readSubitems(){
			return $this->container->readSubitems();
		}

		public function dropSubitem($item_id){
			return $this->container->dropSubitem($item_id);
		}

		public function deleteSubitem($item_id){
			return $this->container->deleteSubitem($item_id);
		}
		public function updateOrder(){
			return $this->container->updateOrder();
		}
	}// end class ItemX
?>
