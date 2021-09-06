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
		private static $database;
		private static $select;
		private static $table;
		private static $where;
		private static $limit;
		private static $query;
		private static $orderBy;
		private static $leftJoinTable;
		private static $leftJoinCondition;
		private static $joinTable;
		private static $joinCondition;

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
		 * @return int
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
		public static function CreateOrUpdate($data, $csrf=true)
		{
			try{
				if (Toolbox::arrayDepth($data) > 1) {
					foreach ($data as $value) {
						self::queryReplace($value, $csrf);
					}
				}
				else {
					self::queryReplace($data, $csrf);
				}
				unset($_SESSION["token"]);
				return true;
			}
			catch(Exception $e) {
				if (Config::isDebug() === 'TRUE') {
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
				return self::queryDelete();
			}
			catch(Exception $e) {
				if (Config::isDebug() === 'TRUE') {
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
		public static function find($id, $filter=true)
		{
			return self::where("id = '{$id}'")->getOne($filter);
		}

		/**
		 * 使用fetch找特定query資料
		 *
		 * @param  boolean $filter 是否過濾
		 * @return object
		 */
		public static function first($filter=true)
		{
			return self::getOne($filter);
		}
	
		/**
		 * 使用fetchAll取得資料
		 *
		 * @param  boolean $filter 是否過濾
		 * @return object
		 */
		public static function get($filter=true)
		{
			return self::getAll($filter);
		}

		/**
		 * PDO取全部的值
		 *
		 * @param  boolean $filter 是否過濾
		 * @return object
		 */
		private static function getAll($filter=true)
		{
			self::querySelect();
			$db = self::connection();
			$sth = $db->prepare(static::$query);
			$sth->execute();
			self::Reset();
			return !$filter ? $sth->fetchAll(PDO::FETCH_OBJ) : Security::defendFilter($sth->fetchAll(PDO::FETCH_OBJ));
		}
	
		/**
		 * PDO取單一筆的值
		 *
		 * @param  boolean $filter 是否過濾
		 * @return object
		 */		
		private static function getOne($filter=true)
		{
			self::querySelect();
			$db = self::connection();
			$sth = $db->prepare(static::$query);
			$sth->execute();
			self::Reset();
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
		public static function insert($data, $getInsertId = false, $csrf=true)
		{
			try{
				return self::queryInsert($data, $getInsertId, $csrf);
			}
			catch(Exception $e) {
				if (Config::isDebug() === 'TRUE') {
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
			static::$joinTable[] = func_get_args()[0];
			static::$joinCondition[] = func_get_args()[1];
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
			static::$leftJoinTable = $table;
			static::$leftJoinCondition = $condition;
			return new static;
		}

		/**
		 * 指定取出資料的筆數
		 *
		 * @param integer $limit 
		 * @return void
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
			static::$orderBy = (array)$orderby;
			return new static;
		}

		/**
		 * 產生query並執行刪除的動作
		 *
		 * @param  mixed $data
		 * @return iterable|object
		 */
		private static function queryDelete()
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
		 * @param  boolean $getInsertId
		 * @param  boolean $csrf
		 * @return iterable|object
		 */		
		private static function queryInsert($data, $getInsertId = false, $csrf=true)
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

			$db = self::connection();
			$sql = 'INSERT INTO '.static::$table.' ('.$column.') VALUES ('.$values.')';

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
		private static function queryReplace($data, $csrf=true)
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

			$db = self::connection();
			$sql = 'REPLACE INTO '.static::$table.' ('.$column.') VALUES ('.$values.')';
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
		private static function querySelect()
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
			if (!empty(static::$leftJoinTable) && !empty(static::$leftJoinCondition)) {
				$query[] = "LEFT JOIN";
				$query[] = static::$leftJoinTable;
				$query[] = "ON";
				$query[] = static::$leftJoinCondition;
			}

			// 處理Join的條件跟資料表
			if (!empty(static::$joinTable) && !empty(static::$joinCondition)) {
				for ($i=0; $i < count(static::$joinTable); $i++) { 
					$query[] = "JOIN";
					$query[] = static::$joinTable[$i];
					$query[] = "ON";
					$query[] = static::$joinCondition[$i];
				}
			}

			// 處理WHERE的條件
			if (!empty(static::$where)) {
				$query[] = "WHERE";
				$query[] = static::$where;
			}

			// 處理Order By排序
			if (!empty(static::$orderBy)) {
				$query[] = "ORDER BY";
				for ($i=0; $i < count(static::$orderBy); $i++) { 
					if (!is_array(static::$orderBy[$i])) {
						throw new Exception('OrderBy參數必須是array([column, sort], ....)');
					}
					$query[] = join(' ', static::$orderBy[$i]);
					if ($i < count(static::$orderBy)-1) {
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
		 * @param  boolean $csrf
		 * @return iterable|object
		 */
		private static function queryUpdate($data, $csrf=true)
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
			static::$leftJoinTable = '';
			static::$leftJoinCondition = '';
			static::$joinTable  = array();
			static::$joinCondition = array();
			static::$orderBy = '';
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
		public static function update($data, $csrf=true)
		{
			try{
				return self::queryUpdate($data, $csrf);
			}
			catch(Exception $e) {
				if (Config::isDebug() === 'TRUE') {
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
		public static function where($where) 
		{
			static::$where = $where;
			return new static;
		}			
	}
?>