<?php

/**
 * Register rest api Class
 *
 * Used to help create custom post types for Wordpress.
 * @link https://github.com/sagar290/AlpineCustomPost.git
 *
 * @author  Sagar Dash
 * @link    sagardash.com
 * @version 1.0
 * @license https://www.opensource.org/licenses/mit-license.html MIT License
 */


if (!class_exists('AlpineRestApi')) {
    class AlpineRestApi
    {
        public static $methodAvailable = [
            'GET',
            'POST',
            'PUT',
            'PATTCH',
            'DELETE',
            'PURGE',
            'UNLINK',
            'HEAD',
        ];
        protected $class = self::class;
        public static $middleware;
        public static $method;
        public static $prefix;
        public static $namespace;
        public static $endpoint;
        public static $callback;
        public static $permission_callback;
        public static $params;
        public static $pattern = "/{[^.}]*}/";
        public static $groupStack = [];
        // public static $groupNamespace = [];
        public static $groupEndpoint = [];

        public function __construct()
        {

        }

        public static function configRoute(
            $method,
            $namespace,
            $endpoint,
            $callback, 
            $permission_callback = null
            ) {
            $slf = self::class;
            self::$namespace = $namespace;
            self::$endpoint = $endpoint;
            self::$callback = $callback;
            self::$permission_callback = $permission_callback;
            self::$method = $method;

            self::$groupEndpoint[] = [
                'method' => self::$method,
                'namespace' => self::$namespace,
                'endpoint' => self::$endpoint,
                'callback' => self::$callback,
                'permission_callback' => self::$permission_callback,
            ];

            self::registerRoute();

            // return new static();
        }

        public static function registerRoute()
        {
            foreach (self::$groupEndpoint as $endpoint) {
                add_action('rest_api_init', function () use ($endpoint) {
                    register_rest_route(
                        $endpoint['namespace'],
                        $endpoint['endpoint'], array(
                            'methods' => $endpoint['method'],
                            'callback' => $endpoint['callback'],
                            // 'permission_callback' => $endpoint['permission_callback'],
                            'permission_callback' => '__return_true',
                        ));
                });
            }

            // var_dump(self::$groupEndpoint);

        }

        public static function group($arr, $callback)
        {
            self::$groupStack = $arr;
            // dd(self::$groupStack);
        }

        public function __set($name, $value)
        {
            $prop = new \ReflectionProperty($this, $name);
            if ($prop->isStatic()) {
                return self::$name = $value;
            } else {
                return $this->$name = $value;
            }
        }

        public function __get($name)
        {
            $prop = new \ReflectionProperty($this, $name);
            if ($prop->isStatic()) {
                return self::$name;
            } else {
                return $this->$name;
            }
        }

        public function __call($name, $arguments)
        {

            if (property_exists($this, $name)) {
                $property = $name;
                $prop = new ReflectionProperty($this, $property);
                if ($prop->isStatic()) {
                    return self::$name;
                } else {
                    return $this->$property;
                }
            }
        }

        public static function __callStatic($name, $arguments)
        {
            if (in_array(strtoupper($name), self::$methodAvailable)) {
                // self::setInstance('method', strtoupper($name));

                
                self::configRoute(
                    strtoupper($name), 
                    $arguments[0], 
                    $arguments[1], 
                    $arguments[2], 
     
                    isset($arguments[3])  ?  $arguments[3] : ['self', 'default_permiddion'] 
                );
            }
        }

        protected function init()
        {
            self::$class = new static();
        }

        protected static function setInstance($name, $value)
        {
            // echo $value;
            $self = new static();
            $self->$name = $value;
            // return new static();
        }

        protected static function getInstance($name)
        {
            $self = new static();
            return $self->$name;
        }

        public  function default_permiddion() {
            // Restrict endpoint to only users who have the edit_posts capability.
            if ( ! current_user_can( 'edit_posts' ) ) {
                return new WP_Error( 'rest_forbidden', esc_html__( 'OMG you can not view private data.'), array( 'status' => 401 ) );
            }
            return true;
        }

    }

}
