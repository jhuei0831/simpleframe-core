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
        public function count();    
            
        /**
		 * 新增或更新資料庫資料
		 *
		 * @param  array $data
		 * @param  bool $csrf
		 * @return bool
		 */
        public function createOrUpdate(array $data, bool $csrf = true): bool;

        /**
		 * 使用指定資料庫
		 *
		 * @param  string $db
		 * @return object
		 */
		public function database(string $db): object;
                
        /**
		 * 刪除指定資料庫資料
		 *
		 * @return boolean
		 */
        public function delete();

        /**
		 * 使用fetch找特定id資料
		 *
		 * @param  string $id
		 * @param  bool   $filter 是否過濾
		 * @return object
		 */
		public function find(string $id, bool $filter = true): object;

        /**
		 * 使用fetch找特定query資料
		 *
		 * @param  bool $filter 是否過濾
		 * @return object
		 */
		public function first(bool $filter = true): object;

        /**
		 * 使用fetchAll取得資料
		 *
		 * @param  bool $filter 是否過濾
		 * @return array
		 */
		public function get(bool $filter = true): array;

		/**
		 * 取得合併資料欄位
		 *
		 * @return object
		 */
		public function groupBy(): object;

        /**
		 * 資料庫中插入一筆新的資料
		 *
		 * @param  array $data
		 * @param  bool  $csrf
		 * @return iterable|object
		 */
		public function insert($data, bool $csrf = true);

        /**
		 * Join 每次Query可以無限使用
		 *
		 * @param  mixed $table 要Join的資料表
		 * @param  mixed $condition Join的條件
		 * @return object
		 */
        public function join($table, $condition);

        /**
		 * leftJoin 每次Query只能使用一次
		 *
		 * @param  mixed $table 要Join的資料表
		 * @param  mixed $condition Join的條件
		 * @return object
		 */
        public function leftJoin($table, $condition);

        /**
		 * 指定取出資料的筆數
		 *
		 * @return object
		 */
		public function limit(): object;

        /**
		 * 指定要排序的欄位
		 *
		 * @param  array $orderby
		 * @return void
		 */
		public function orderby(array $orderby): object;

        /**
		 * 設定選擇的欄位
		 *
		 * @return object
		 */
		public function select(): object;

        /**
		 * 設定選擇的資料表
		 *
		 * @param  string $table
		 * @return object
		 */
		public function table(string $table): object;

        /**
		 * 更新指定資料庫資料
		 *
		 * @param  array $data
		 * @return void
		 */
		public function update(array $data, bool $csrf = true);

        /**
		 * 設定對資料庫進行動作的條件(就是query的WHERE)
		 *
		 * @param  string $where
		 * @return object
		 */
		public function where(string $where): object;
    }