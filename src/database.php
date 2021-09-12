<?php
	namespace Kerwin\Core;

	use PDO;
	use Exception;

	use Kerwin\Core\Contracts\Database\Query;
	use Kerwin\Core\Support\Config;
	use Kerwin\Core\Support\Toolbox;
	use Kerwin\Core\Support\Facades\Security;

	class Database implements Query
	{
		private $config;
		private $database;
		private $select;
		private $table;
		private $where;
		private $limit;
		private $query;
		private $orderBy;
		private $leftJoinTable;
		private $leftJoinCondition;
		private $joinTable;
		private $joinCondition;

		public function __construct() {
			$this->config = new Config();
		}

	    /**
	     * 與資料庫進行連線
	     *
	     * @return object
	     */
	    public function connection()
		{
			// 資料庫預設值
			$host     = $_ENV['DB_HOST'];
			$database = $this->database != null ? $this->database : $_ENV['DB_DATABASE'];
			$account  = $_ENV['DB_USERNAME'];
			$password = $_ENV['DB_PASSWORD'];
			$charset  = $_ENV['DB_CHARSET'];

			$dsn = "mysql:host={$host};dbname={$database}";
			if($charset != "" && $charset != null){
				$dsn .= ";charset={$charset}";
			}
				
			try
			{
				$connection = new PDO(
					$dsn,
					$account,
					$password,
					array(
						PDO::ATTR_EMULATE_PREPARES => false,
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						PDO::MYSQL_ATTR_INIT_COMMAND=>"SET sql_mode='TRADITIONAL';" 
					)
				);
				if($charset != "" && $charset != null)
				{
					$connection->exec("SET CHARACTER SET {$charset}");
					$connection->exec("SET NAMES {$charset}");
				}
				return $connection;
			}
			catch(Exception $ex)
			{
				$this->connection();
			}
		}

		/**
		 * 取得資料的總數
		 *
		 * @param  string $db
		 * @return int
		 */
		public function count()
		{
			return count($this->getAll());
		}

		/**
		 * 新增或更新資料庫資料
		 *
		 * @return boolean
		 */
		public function CreateOrUpdate($data, $csrf=true)
		{
			try{
				if (Toolbox::arrayDepth($data) > 1) {
					foreach ($data as $value) {
						$this->queryReplace($value, $csrf);
					}
				}
				else {
					$this->queryReplace($data, $csrf);
				}
				unset($_SESSION["token"]);
				return true;
			}
			catch(Exception $e) {
				if ($this->config->isDebug() === 'TRUE') {
					throw $e;
				}
				else{
					return false;
				}
			}
		}
		
		/**
		 * 使用指定資料庫
		 *
		 * @param  string $db
		 * @return object
		 */
		public function database($db)
		{
			$this->database = $db;
			return $this;
		}
		
		/**
		 * 刪除指定資料庫資料
		 *
		 * @return boolean
		 */
		public function delete()
		{
			try{
				return $this->queryDelete();
			}
			catch(Exception $e) {
				if ($this->config->isDebug() === 'TRUE') {
					throw $e;
				}
				else{
					return false;
				}
			}
		}
	
		/**
		 * 使用fetch找特定id資料
		 *
		 * @param  string $id
		 * @param  boolean $filter 是否過濾
		 * @return object
		 */
		public function find($id, $filter=true)
		{
			return $this->where("id = '{$id}'")->getOne($filter);
		}

		/**
		 * 使用fetch找特定query資料
		 *
		 * @param  boolean $filter 是否過濾
		 * @return object
		 */
		public function first($filter=true)
		{
			return $this->getOne($filter);
		}
	
		/**
		 * 使用fetchAll取得資料
		 *
		 * @param  boolean $filter 是否過濾
		 * @return object
		 */
		public function get($filter=true)
		{
			return $this->getAll($filter);
		}

		/**
		 * PDO取全部的值
		 *
		 * @param  boolean $filter 是否過濾
		 * @return object
		 */
		private function getAll($filter=true)
		{
			$this->querySelect();
			$db = $this->connection();
			$sth = $db->prepare($this->query);
			$sth->execute();
			$this->Reset();
			return !$filter ? $sth->fetchAll(PDO::FETCH_OBJ) : Security::defendFilter($sth->fetchAll(PDO::FETCH_OBJ));
		}
	
		/**
		 * PDO取單一筆的值
		 *
		 * @param  boolean $filter 是否過濾
		 * @return object
		 */		
		private function getOne($filter=true)
		{
			$this->querySelect();
			$db = $this->connection();
			$sth = $db->prepare($this->query);
			$sth->execute();
			$this->Reset();
			return !$filter ? $sth->fetch(PDO::FETCH_OBJ) : Security::defendFilter($sth->fetch(PDO::FETCH_OBJ));
		}

		/**
		 * 資料庫中插入一筆新的資料
		 *
		 * @param  array $data
		 * @param  boolean $getInsertId
		 * @param  boolean $csrf
		 * @return iterable|object
		 */
		public function insert($data, $getInsertId = false, $csrf=true)
		{
			try{
				return $this->queryInsert($data, $getInsertId, $csrf);
			}
			catch(Exception $e) {
				if ($this->config->isDebug() === 'TRUE') {
					throw $e;
				}
				else{
					return false;
				}
			}
		}

		/**
		 * Join 每次Query可以無限使用
		 *
		 * @param  mixed $table 要Join的資料表
		 * @param  mixed $condition Join的條件
		 * @return object
		 */
		public function Join($table, $condition)
		{
			$this->joinTable[] = func_get_args()[0];
			$this->joinCondition[] = func_get_args()[1];
			return $this;
		}
		
		/**
		 * leftJoin 每次Query只能使用一次
		 *
		 * @param  mixed $table 要Join的資料表
		 * @param  mixed $condition Join的條件
		 * @return object
		 */
		public function leftJoin($table, $condition)
		{
			$this->leftJoinTable = $table;
			$this->leftJoinCondition = $condition;
			return $this;
		}

		/**
		 * 指定取出資料的筆數
		 *
		 * @param integer $limit 
		 * @return void
		 */
		public function limit() {
			$this->limit = func_get_args();
			return $this;
		}
		
		/**
		 * 指定要排序的欄位
		 *
		 * @param  mixed $orderby
		 * @param  mixed $type
		 * @return void
		 */
		public function orderby($orderby)
		{
			$this->orderBy = (array)$orderby;
			return $this;
		}

		/**
		 * 產生query並執行刪除的動作
		 *
		 * @param  mixed $data
		 * @return iterable|object
		 */
		private function queryDelete()
		{
			if (empty($this->where)) {
				throw new Exception('Delete比需要有WHERE條件');
			}

			$db = $this->connection();
			$sql = 'DELETE FROM '.$this->table.' WHERE '.$this->where;
			$sth = $db->prepare($sql);
			return $sth->execute();
		}

		/**
		 * 產生query並執行新增的動作
		 *
		 * @param  array $data
		 * @param  boolean $getInsertId
		 * @param  boolean $csrf
		 * @return iterable|object
		 */		
		private function queryInsert($data, $getInsertId = false, $csrf=true)
		{
			// 輸入只能是array型態
			if (!is_array($data)) {
				throw new Exception('Insert的參數必須是array');
			}

			// CSRF驗證
			if ($csrf && Security::checkCSRF($data)) {
				unset($data['token']);
				unset($_SESSION["token"]);
			}

			$column = '';
			$values = '';
			
			//表單欄位名稱→資料表欄位名稱，表單欄位資料→資料表欄位資料，去除最後的逗點
			foreach ($data as $key => $value) 
			{
				$attr = $key;
				$column .= $attr.',';
				$values .= ':'.$attr.',';
			}

			$column = substr($column, 0, -1);
			$values = substr($values, 0, -1);

			$db = $this->connection();
			$sql = 'INSERT INTO '.$this->table.' ('.$column.') VALUES ('.$values.')';

			$sth = $db->prepare($sql);
			foreach ($data as $key => $value) 
			{
				$attr = $key;
				$sth->bindValue(':'.$attr, $value);	
			}
						
			if ($getInsertId) {
				$sth->execute();
				return $db->lastInsertId();
			}
			else {
				return $sth->execute();
			}
		}

		/**
		 * 產生query並執行更新或新增的動作
		 *
		 * @param  array $data
		 * @param  boolean $csrf
		 * @return iterable|object
		 */
		private function queryReplace($data, $csrf=true)
		{
			// 輸入只能是array型態
			if (!is_array($data)) {
				throw new Exception('Replace的參數必須是array');
			}

			// CSRF驗證
			if ($csrf && Security::checkCSRF($data)) {
				unset($data['token']);
				unset($_SESSION["token"]);
			}

			$column = '';
			$values = '';
			
			//表單欄位名稱→資料表欄位名稱，表單欄位資料→資料表欄位資料，去除最後的逗點
			foreach ($data as $key => $value) 
			{
				$attr = $key;
				$column .= $attr.',';
				$values .= ':'.$attr.',';
			}

			$column = substr($column, 0, -1);
			$values = substr($values, 0, -1);

			$db = $this->connection();
			$sql = 'REPLACE INTO '.$this->table.' ('.$column.') VALUES ('.$values.')';
			$sth = $db->prepare($sql);
			foreach ($data as $key => $value) 
			{
				$attr = $key;
				$sth->bindValue(':'.$attr, $value);	
			}
			return $sth->execute();
		}
				
		/**
		 * 產生要搜尋的query
		 *
		 * @return object
		 */
		private function querySelect()
		{
			$query[] = "SELECT";
			// 如果select空值或*字號，則取全部
			if (empty($this->select) || $this->select == '*') {
				$query[] = "*";  
			}
			else {
				$query[] = join(', ', $this->select);
			}
	
			$query[] = "FROM";
			$query[] = $this->table;
			
			// 處理LeftJoin的條件跟資料表
			if (!empty($this->leftJoinTable) && !empty($this->leftJoinCondition)) {
				$query[] = "LEFT JOIN";
				$query[] = $this->leftJoinTable;
				$query[] = "ON";
				$query[] = $this->leftJoinCondition;
			}

			// 處理Join的條件跟資料表
			if (!empty($this->joinTable) && !empty($this->joinCondition)) {
				for ($i=0; $i < count($this->joinTable); $i++) { 
					$query[] = "JOIN";
					$query[] = $this->joinTable[$i];
					$query[] = "ON";
					$query[] = $this->joinCondition[$i];
				}
			}

			// 處理WHERE的條件
			if (!empty($this->where)) {
				$query[] = "WHERE";
				$query[] = $this->where;
			}

			// 處理Order By排序
			if (!empty($this->orderBy)) {
				$query[] = "ORDER BY";
				for ($i=0; $i < count($this->orderBy); $i++) { 
					if (!is_array($this->orderBy[$i])) {
						throw new Exception('OrderBy參數必須是array([column, sort], ....)');
					}
					$query[] = join(' ', $this->orderBy[$i]);
					if ($i < count($this->orderBy)-1) {
						$query[] = ', ';
					}
				}		
			}

			// 處理Limit資料數量
			if (!empty($this->limit)) {
				$query[] = "LIMIT";
				$query[] = join(', ', $this->limit);;
			}
			
			$this->query = join(' ', $query);
			
			return $this->query;
		}
		
		/**
		 * 產生query並執行更新的動作
		 *
		 * @param  array $data
		 * @param  boolean $csrf
		 * @return iterable|object
		 */
		private function queryUpdate($data, $csrf=true)
		{
			// 輸入只能是array型態
			if (!is_array($data)) {
				throw new Exception('Update的參數必須是array');
			}

			// CSRF驗證
			if ($csrf && Security::checkCSRF($data)) {
				unset($data['token']);
				unset($_SESSION["token"]);
			}

			// 更新必須要有WHERE條件
			if (empty($this->where)) {
				throw new Exception('更新必須要有WHERE條件');
			}

			$sql_cmd = ''; //表單欄位名稱→資料表欄位名稱，表單欄位資料→資料表欄位資料，去除最後的逗點
			foreach ($data as $key => $value) 
			{
				$attr = $key;
				$sql_cmd .= $attr.'=:'.$attr.',';
			}
			$sql_cmd = substr($sql_cmd, 0, -1);

			$db = $this->connection();
			$sql = 'UPDATE '.$this->table.' SET '.$sql_cmd.' WHERE '.$this->where;
			$sth = $db->prepare($sql);
			foreach ($data as $key => $value) 
			{
				$attr = $key;
				$sth->bindValue(':'.$attr, $value);	
			}
			return $sth->execute();
		}
		
		/**
		 * 將靜態變數重置以免變數被延用
		 *
		 * @return object
		 */
		private function Reset() {
			$this->select = array();
			$this->table = '';
			$this->where = '';
			$this->limit = '';
			$this->query = '';
			$this->leftJoinTable = '';
			$this->leftJoinCondition = '';
			$this->joinTable  = array();
			$this->joinCondition = array();
			$this->orderBy = '';
		}

		/**
		 * 設定選擇的欄位
		 *
		 * @return object
		 */
		public function select() {
			$this->select = func_get_args();
			return $this;
		}

		/**
		 * 設定選擇的資料表
		 *
		 * @param  string $table
		 * @return object
		 */
		public function table($table)
		{
			$this->table = $table;
			return $this;
		}

		/**
		 * 更新指定資料庫資料
		 *
		 * @param  array $data
		 * @return void
		 */
		public function update($data, $csrf=true)
		{
			try{
				return $this->queryUpdate($data, $csrf);
			}
			catch(Exception $e) {
				if ($this->config->isDebug() === 'TRUE') {
					throw $e;
				}
				else{
					return false;
				}
			}
		}
		
		/**
		 * 設定對資料庫進行動作的條件(就是query的WHERE)
		 *
		 * @param  string $where
		 * @return object
		 */
		public function where($where) 
		{
			$this->where = $where;
			return $this;
		}			
	}
?>