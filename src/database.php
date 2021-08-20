<?php
	namespace Kerwin\Core;

	use PDO;
	use Exception;

	use Kerwin\Core\Config;
	use Kerwin\Core\Security;

	class Database
	{
		private static $database;
		private static $select = array();
		private static $table;
		private static $where;
		private static $limit;
		private static $query;
		private static $order_by;
		private static $leftJoin_table;
		private static $leftJoin_condition;
		private static $join_table;
		private static $join_condition;
		
		private static $config;

        public function __construct() {
            $this->config = new Config();
        }

	    /**
	     * 與資料庫進行連線
	     *
	     * @return object
	     */
	    public static function connection()
		{
			// 資料庫預設值
			$host     = $_ENV['DB_HOST'];
			$database = static::$database != null ? static::$database : $_ENV['DB_DATABASE'];
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
				self::connection();
			}
		}

		/**
		 * 取得資料的總數
		 *
		 * @param  string $db
		 * @return object
		 */
		public static function count()
		{
			return count(self::getAll());
		}

		/**
		 * 新增或更新資料庫資料
		 *
		 * @return boolean
		 */
		public static function CreateOrUpdate($data)
		{
			try{
				return self::query_replace($data);
			}
			catch(Exception $e) {
				if (self::$config->is_debug() === 'TRUE') {
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
		public static function database($db)
		{
			static::$database = $db;
			return new static;
		}
		
		/**
		 * 刪除指定資料庫資料
		 *
		 * @return boolean
		 */
		public static function delete()
		{
			try{
				return self::query_delete();
			}
			catch(Exception $e) {
				if (self::$config->is_debug() === 'TRUE') {
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
		 * @param  mixed $filter 是否過濾
		 * @return array
		 */
		public static function find($id, $filter=true)
		{
			return self::where("id = '{$id}'")->getOne($filter);
		}

		/**
		 * 使用fetch找特定query資料
		 *
		 * @param  mixed $filter 是否過濾
		 * @return array
		 */
		public static function first($filter=true)
		{
			return self::getOne($filter);
		}
	
		/**
		 * 使用fetchAll取得資料
		 *
		 * @param  mixed $filter 是否過濾
		 * @return array
		 */
		public static function get($filter=true)
		{
			return self::getAll($filter);
		}

		/**
		 * PDO取全部的值
		 *
		 * @param  mixed $filter 是否過濾
		 * @return array
		 */
		private static function getAll($filter=true)
		{
			self::query_select();
			$db = self::connection();
			$sth = $db->prepare(static::$query);
			$sth->execute();
			self::Reset();
			return !$filter ? $sth->fetchAll(PDO::FETCH_OBJ) : Security::defend_filter($sth->fetchAll(PDO::FETCH_OBJ));
		}
	
		/**
		 * PDO取單一筆的值
		 *
		 * @param  mixed $filter 是否過濾
		 * @return void
		 */		
		private static function getOne($filter=true)
		{
			self::query_select();
			$db = self::connection();
			$sth = $db->prepare(static::$query);
			$sth->execute();
			self::Reset();
			return !$filter ? $sth->fetch(PDO::FETCH_OBJ) : Security::defend_filter($sth->fetch(PDO::FETCH_OBJ));
		}

		/**
		 * insert
		 *
		 * @param  array $data
		 * @return iterable|object
		 */
		public static function insert($data, $getInsertId = false)
		{
			try{
				return self::query_insert($data, $getInsertId);
			}
			catch(Exception $e) {
				if (self::$config->is_debug() === 'TRUE') {
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
		public static function Join($table, $condition)
		{
			static::$join_table[] = func_get_args()[0];
			static::$join_condition[] = func_get_args()[1];
			return new static;
		}
		
		/**
		 * leftJoin 每次Query只能使用一次
		 *
		 * @param  mixed $table 要Join的資料表
		 * @param  mixed $condition Join的條件
		 * @return object
		 */
		public static function leftJoin($table, $condition)
		{
			static::$leftJoin_table = $table;
			static::$leftJoin_condition = $condition;
			return new static;
		}

		/**
		 * 指定取出資料的筆數
		 *
		 * @param integer $limit 
		 * @return object
		 */
		public static function limit() {
			static::$limit = func_get_args();
			return new static;
		}
		
		/**
		 * 指定要排序的欄位
		 *
		 * @param  mixed $orderby
		 * @param  mixed $type
		 * @return void
		 */
		public static function orderby($orderby)
		{
			static::$order_by = (array)$orderby;
			return new static;
		}

		/**
		 * 產生query並執行刪除的動作
		 *
		 * @param  mixed $data
		 * @return iterable|object
		 */
		private static function query_delete()
		{
			if (empty(static::$where)) {
				throw new Exception('Delete比需要有WHERE條件');
			}

			$db = self::connection();
			$sql = 'DELETE FROM '.static::$table.' WHERE '.static::$where;
			$sth = $db->prepare($sql);
			return $sth->execute();
		}

		/**
		 * 產生query並執行新增的動作
		 *
		 * @param  array $data
		 * @param  mixed $getInsertId
		 * @return iterable|object
		 */		
		private static function query_insert($data, $getInsertId = false)
		{
			// 輸入只能是array型態
			if (!is_array($data)) {
				throw new Exception('Insert的參數必須是array');
			}

			// CSRF驗證
			if (Security::check_csrf($data)) {
				unset($data['token']);
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

			$db = self::connection();
			$sql = 'INSERT INTO '.static::$table.' ('.$column.') VALUES ('.$values.')';

			$sth = $db->prepare($sql);
			foreach ($data as $key => $value) 
			{
				$attr = $key;
				$sth->bindValue(':'.$attr, $value);	
			}
			
			unset($_SESSION["token"]);
			
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
		 * @return iterable|object
		 */
		private static function query_replace($data)
		{
			// 輸入只能是array型態
			if (!is_array($data)) {
				throw new Exception('Replace的參數必須是array');
			}

			// CSRF驗證
			if (Security::check_csrf($data)) {
				unset($data['token']);
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

			$db = self::connection();
			$sql = 'REPLACE INTO '.static::$table.' ('.$column.') VALUES ('.$values.')';
			$sth = $db->prepare($sql);
			foreach ($data as $key => $value) 
			{
				$attr = $key;
				$sth->bindValue(':'.$attr, $value);	
			}
			unset($_SESSION["token"]);
			return $sth->execute();
		}
				
		/**
		 * 產生要搜尋的query
		 *
		 * @return object
		 */
		private static function query_select()
		{
			$query[] = "SELECT";
			// 如果select空值或*字號，則取全部
			if (empty(static::$select) || static::$select == '*') {
				$query[] = "*";  
			}
			else {
				$query[] = join(', ', static::$select);
			}
	
			$query[] = "FROM";
			$query[] = static::$table;
			
			// 處理LeftJoin的條件跟資料表
			if (!empty(static::$leftJoin_table) && !empty(static::$leftJoin_condition)) {
				$query[] = "LEFT JOIN";
				$query[] = static::$leftJoin_table;
				$query[] = "ON";
				$query[] = static::$leftJoin_condition;
			}

			// 處理Join的條件跟資料表
			if (!empty(static::$join_table) && !empty(static::$join_condition)) {
				for ($i=0; $i < count(static::$join_table); $i++) { 
					$query[] = "JOIN";
					$query[] = static::$join_table[$i];
					$query[] = "ON";
					$query[] = static::$join_condition[$i];
				}
			}

			// 處理WHERE的條件
			if (!empty(static::$where)) {
				$query[] = "WHERE";
				$query[] = static::$where;
			}

			// 處理Order By排序
			if (!empty(static::$order_by)) {
				$query[] = "ORDER BY";
				for ($i=0; $i < count(static::$order_by); $i++) { 
					if (!is_array(static::$order_by[$i])) {
						throw new Exception('OrderBy參數必須是array([column, sort], ....)');
					}
					$query[] = join(' ', static::$order_by[$i]);
					if ($i < count(static::$order_by)-1) {
						$query[] = ', ';
					}
				}		
			}

			// 處理Limit資料數量
			if (!empty(static::$limit)) {
				$query[] = "LIMIT";
				$query[] = join(', ', static::$limit);;
			}
			
			static::$query = join(' ', $query);
			
			return static::$query;
		}
		
		/**
		 * 產生query並執行更新的動作
		 *
		 * @param  array $data
		 * @return iterable|object
		 */
		private static function query_update($data)
		{
			// 輸入只能是array型態
			if (!is_array($data)) {
				throw new Exception('Update的參數必須是array');
			}

			// CSRF驗證
			if (Security::check_csrf($data)) {
				unset($data['token']);
			}

			// 更新必須要有WHERE條件
			if (empty(static::$where)) {
				throw new Exception('更新必須要有WHERE條件');
			}

			$sql_cmd = ''; //表單欄位名稱→資料表欄位名稱，表單欄位資料→資料表欄位資料，去除最後的逗點
			foreach ($data as $key => $value) 
			{
				$attr = $key;
				$sql_cmd .= $attr.'=:'.$attr.',';
			}
			$sql_cmd = substr($sql_cmd, 0, -1);

			$db = self::connection();
			$sql = 'UPDATE '.static::$table.' SET '.$sql_cmd.' WHERE '.static::$where;
			$sth = $db->prepare($sql);
			foreach ($data as $key => $value) 
			{
				$attr = $key;
				$sth->bindValue(':'.$attr, $value);	
			}
			unset($_SESSION["token"]);
			return $sth->execute();
		}
		
		/**
		 * 將靜態變數重置以免變數被延用
		 *
		 * @return object
		 */
		private static function Reset() {
			static::$select = array();
			static::$table = '';
			static::$where = '';
			static::$limit = '';
			static::$query = '';
			static::$leftJoin_table = '';
			static::$leftJoin_condition = '';
			static::$join_table  = array();
			static::$join_condition = array();
			static::$order_by = '';
		}

		/**
		 * 設定選擇的欄位
		 *
		 * @return object
		 */
		public static function select() {
			static::$select = func_get_args();
			return new static;
		}

		/**
		 * 設定選擇的資料表
		 *
		 * @param  string $table
		 * @return object
		 */
		public static function table($table)
		{
			static::$table = $table;
			return new static;
		}

		/**
		 * 更新指定資料庫資料
		 *
		 * @param  array $data
		 * @return void
		 */
		public static function update($data)
		{
			try{
				return self::query_update($data);
			}
			catch(Exception $e) {
				if (self::$config->is_debug() === 'TRUE') {
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
		public static function where($where) {
			static::$where = $where;
			return new static;
		}			
	}
?>