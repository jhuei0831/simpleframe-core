<?php

    namespace Kerwin\Core\Support;

    use Ramsey\Uuid\Uuid;

    class Toolbox {
        
        /**
         * breadcrumb 路徑
         *
         * @param  string $home 首頁url
         * @param  array $breadcrumbs 如果是當下頁面url用#
         * @return void
         */
        public static function breadcrumb($home, $breadcrumbs)
        {
            // HOME PAGE
            echo '<nav class="bg-white border-b border-t border-gray-200 flex" aria-label="Breadcrumb">
                    <ol class="w-full mx-auto px-4 flex space-x-4 sm:px-6 lg:px-8">
                        <li class="flex">
                            <div class="flex items-center">
                                <a href="'.$home.'" class="text-gray-400 hover:text-gray-500">
                                    <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                    </svg>
                                    <span class="sr-only">Home</span>
                                </a>
                            </div>
                        </li>';
            // CHILD PAGE
            foreach ($breadcrumbs as $name => $url) {
                echo "<li class='flex'>
                    <div class='flex items-center'>
                        <svg class='flex-shrink-0 w-6 h-full text-gray-200' viewBox='0 0 24 44' preserveAspectRatio='none' fill='currentColor' xmlns='http://www.w3.org/2000/svg' aria-hidden='true'>
                            <path d='M.293 0l22 22-22 22h1.414l22-22-22-22H.293z' />
                        </svg>
                        <a href='{$url}' class='".($url=='#'?'pointer-events-none text-cyan-600':'text-gray-500')." ml-4 text-sm font-medium hover:text-gray-700'>{$name}</a>
                    </div>
                </li>";
            }
            echo '</ol>
            </nav>';
        }
        
        /**
         * 陣列深度
         *
         * @param  array $array
         * @return int
         */
        public static function arrayDepth(array $array): int
        {
            $maxDepth = 1;
            foreach ($array as $value) {
                if (is_array($value)) {
                    $depth = self::arrayDepth($value) + 1;

                    if ($depth > $maxDepth) {
                        $maxDepth = $depth;
                    }
                }
            }
            return $maxDepth;
        }
                
        /**
         * 移除陣列中不要的鍵值
         *
         * @param  array $array
         * @param  array|string $keys
         * @return void
         */
        public static function forget(&$array, $keys)
        {
            $keys = (array) $keys;

            if (count($keys) === 0) {
                return;
            }

            foreach ($keys as $key) {
                if (array_key_exists($key, $array)) {
                    unset($array[$key]);
                }
            }
        }
        
        /**
         * 執行forgot函式
         *
         * @param  array $array
         * @param  array|string $keys
         * @return array
         */
        public static function except($array, $keys)
        {
            static::forget($array, $keys);

            return $array;
        }
        
        /**
         * 只留下陣列中所需要的鍵值
         *
         * @param  array $array
         * @param  array|string $keys
         * @return array
         */
        public static function only($array, $keys)
        {
            $keys = (array) $keys;
            return array_intersect_key($array, array_flip($keys));
        }

		/**
		 * 產生version 4 UUID
		 *
		 * @return void
		 */
		public static function UUIDv4() {
		    return Uuid::uuid4();
		}
    }