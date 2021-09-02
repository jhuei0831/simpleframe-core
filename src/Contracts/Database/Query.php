<?php

    namespace Kerwin\Core\Contracts\Database;
    
    interface Query 
    {        
        /**
		 * 取得資料的總數
		 *
		 * @param  string $db
		 * @return object
		 */
        public static function count();    
            
        /**
		 * 新增或更新資料庫資料
		 *
		 * @return boolean
		 */
        public static function CreateOrUpdate($data, $csrf=true);

        /**
		 * 使用指定資料庫
		 *
		 * @param  string $db
		 * @return object
		 */
        public static function database($db);
                
        /**
		 * 刪除指定資料庫資料
		 *
		 * @return boolean
		 */
        public static function delete();

        /**
		 * 使用fetch找特定id資料
		 *
		 * @param  string $id
		 * @param  boolean $filter 是否過濾
		 * @return object
		 */
        public static function find($id, $filter=true);

        /**
		 * 使用fetch找特定query資料
		 *
		 * @param  boolean $filter 是否過濾
		 * @return object
		 */
        public static function first($filter=true);

        /**
		 * 使用fetchAll取得資料
		 *
		 * @param  boolean $filter 是否過濾
		 * @return object
		 */
        public static function get($filter=true);

        /**
		 * 資料庫中插入一筆新的資料
		 *
		 * @param  array $data
		 * @param  boolean $getInsertId
		 * @param  boolean $csrf
		 * @return iterable|object
		 */
        public static function insert($data, $getInsertId = false, $csrf=true);

        /**
		 * Join 每次Query可以無限使用
		 *
		 * @param  mixed $table 要Join的資料表
		 * @param  mixed $condition Join的條件
		 * @return object
		 */
        public static function Join($table, $condition);

        /**
		 * leftJoin 每次Query只能使用一次
		 *
		 * @param  mixed $table 要Join的資料表
		 * @param  mixed $condition Join的條件
		 * @return object
		 */
        public static function leftJoin($table, $condition);

        /**
		 * 指定取出資料的筆數
		 *
		 * @param integer $limit 
		 * @return void
		 */
        public static function limit();

        /**
		 * 指定要排序的欄位
		 *
		 * @param  mixed $orderby
		 * @param  mixed $type
		 * @return void
		 */
        public static function orderby($orderby);

        /**
		 * 設定選擇的欄位
		 *
		 * @return object
		 */
        public static function select();

        /**
		 * 設定選擇的資料表
		 *
		 * @param  string $table
		 * @return object
		 */
		public static function table($table);

        /**
		 * 更新指定資料庫資料
		 *
		 * @param  array $data
		 * @return void
		 */
		public static function update($data, $csrf=true);

        /**
		 * 設定對資料庫進行動作的條件(就是query的WHERE)
		 *
		 * @param  string $where
		 * @return object
		 */
		public static function where($where);
    }