<?php if (!defined('BASE_PATH')) exit('No direct script access allowed');

class App
{
    protected $url = [];
    protected $url_string = '';
    protected $controller_endpoint = '';
    protected $controller = '';
    protected $method_index = 1;
    protected $method = '';
    protected $params = [];
    protected $route = [];
    protected $default_language = '';
    protected $available_languages = [];

    public function __construct($config = [], $route = [])
    {
        /*
         * Set the default Controller and Method to call when they are not specified in the URL.
         */
        $this->controller = $config['default_controller'];
        $this->method = $config['default_method'];

        /*
         * Set the default language and available languages that can be specified in the URL.
         * Example: http://domain.com/en/user/1 (Where "en" is specifying English).
         */
        $this->default_language = $config['default_language'];
        $this->available_languages = $config['available_languages'];

        /*
         * Parse the URL having the format of /controller/method/param1/param2 into an array.
         * Remove the Controller and Method indexes from the array  after we obtain them
         * so that only the parameters remain for us to pass on to the defined method.
         *
         * The controller file name and Class name must have use a CamelCase format like: Products, MyProducts
         * However, when calling these controllers from the URL use lowercase with hyphens.
         * Examples:
         * Calling: /my-products/list results in calling the Controller 'MyProducts' and the method 'list'.
         * Calling: /admin/top-sales/list results in calling the Controller 'TopSales' and the method 'list' from the sub-directory '/Admin/'.
         *
         * Controllers may also be nested in unlimited sub-directories allowing for the reuse of Controller names:
         * Examples:
         * Get Basic User: /user/get/1 (/controller/method/param1)
         * Get Admin User: /admin/user/get/1 (/directory/controller/method/param1)
         *
         * You may exclude adding the "index" method to the URL as this is set by default.
         * It will affect the parameters that follow in the URL.
         * Examples:
         * /sales-report/index/january is the equivalent to /sales-report/january
         * Which loads the SalesReport controller and calls the "index" method passing to it "january".
         *
         */
        $this->url = $this->parseUrl($route, $config);

        // Determine the language segment from the URL, or use an empty string if not available.
        $first_url_segment = $this->url[0] ?? '';

        // Check if the URL segment matches an available language.
        $language = in_array($first_url_segment, $config['available_languages']) ? strtolower($first_url_segment) : strtolower($this->default_language);

        // Set the language file path.
        $language_file = LANGUAGE_PATH . $language . '_lang.php';

        // Load the language file if it exists.
        if (file_exists($language_file) && (!defined('LANG') || $language !== strtolower($this->default_language))) {
            require_once($language_file);
            setcookie("language", $language, time() + (60 * 60 * 24 * 30));

            // Remove the language segment from the URL array if it was part of the URL.
            if ($language === strtolower($first_url_segment)) {
                unset($this->url[0]);
            }
        }


        // Initialize the controller endpoint and set a flag for existence.
        $controller_exists = false;

        // Walk down the URL assuming the previous index may be a directory under Controllers.
        foreach ($this->url as $key => $value) {
            // Convert the value to CamelCase and append to the controller endpoint.
            $this->controller_endpoint .= $this->dashesToCamelCase($value);

            // Check if the controller file exists.
            $controller_path = CONTROLLERS_PATH . $this->controller_endpoint . '.php';
            if (file_exists($controller_path)) {
                $this->controller = $this->dashesToCamelCase($value);
                $this->method_index = $key + 1;
                unset($this->url[$key]);
                $controller_exists = true;
                break;
            }

            // Handle default controller path case sensitivity.
            switch ($config['default_controller_path_case']) {
                case 'lowercase':
                    $this->controller_endpoint = strtolower($this->controller_endpoint);
                    break;
                case 'firstlettercap':
                    $this->controller_endpoint = ucfirst($this->controller_endpoint);
                    break;
            }

            unset($this->url[$key]);
            $this->controller_endpoint .= '/';
        }


        if(!$controller_exists) {
            $this->controller_endpoint = $this->controller;
        }
        require_once CONTROLLERS_PATH . $this->controller_endpoint . '.php';
        $this->controller = new $this->controller;

        // Set Method or use default.
        if(isset($this->url[$this->method_index]))
        {
            /**
             * Changed from method_exists() to is_callable() allowing for Classes
             * to offer Private methods that cannot be invoked by dynamic URLs such
             * as /class/priavte-method/param
             */
            //if(method_exists($this->controller, $this->url[$this->method_index]))
            if(is_callable(array($this->controller, $this->url[$this->method_index])))
            {
                $this->method = $this->url[$this->method_index];
                unset($this->url[$this->method_index]);
            }
        }

        // Rebase Parameters or set empty array.
        $this->params = $this->url ? array_values($this->url) : [];

        // Send Parameters to the Method of the Controller.
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * @param $string
     * @param bool $capitalizeFirstCharacter
     * @return mixed|string
     *
     * Converts hyphenated-strings to CamelCase
     * If the second parameter is 'true' (default) - "camel-case" returns "CamelCase"
     * If the second parameter is 'false' - "camel-case" returns "camelCase"
     */
    protected function dashesToCamelCase($string, $capitalizeFirstCharacter = true): string
    {
        $str = str_replace('-', '', ucwords($string, '-'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }


    /**
     * @param $url
     * @param $route
     * @return mixed
     *
     * Remaps the $url string provided when a RegEx pattern from $routes is matched,
     * otherwise, return $url unchanged.
     */
    protected function remapUrl($url, $route): string
    {
        foreach ($route as $pattern => $replacement) {
            $pattern = sprintf('/%s/i', str_replace([':any', ':num', '/'], ['(.+)', '(\d+)', '\/'], $pattern));
            $route_url = preg_replace($pattern, "/$replacement/", $url);

            if ($route_url !== $url && $route_url !== null) {
                return $route_url;
            }
        }

        return $url;
    }


    /**
     * @param array $route
     * @param array $config
     * @return array
     *
     * Returns an array containing all parts of the active URL using the method specified in $config.
     * Also, performs URL remapping as needed.
     */
    protected function parseUrl(array $route, array $config = []): array
    {
        if (!isset($config['uri_protocol'])) {
            die('The URI PROTOCOL configuration is not set.');
        }

        $url = '';

        switch ($config['uri_protocol']) {
            case 'PATH_INFO':
                $url = $_SERVER['PATH_INFO'] ?? '';
                break;
            case 'QUERY_STRING':
                $url = isset($_SERVER['QUERY_STRING']) ? explode('=', $_SERVER['QUERY_STRING'])[1] : '';
                break;
            case 'REQUEST_URI':
                $url = $_SERVER['REQUEST_URI'] ?? '';
                $url = ltrim($url, '/');
                break;
            default:
                $url = $_GET['url'] ?? '';
                break;
        }

        if (empty($url)) {
            return [];
        }

        $this->url_string = $this->remapUrl(filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL), $route);
        return explode('/', $this->url_string);
    }
}