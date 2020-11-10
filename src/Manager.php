<?php
namespace Enjoys\Core\URL;

use Enjoys\Core\Request;

class Manager {
    
    use \Enjoys\Traits\Options;
    
    /**
     *
     * @var type 
     */
    private $rules = [];
   /**
     * @var string the URL suffix used when [[enablePrettyUrl]] is `true`.
     * For example, ".html" can be used so that the URL looks like pointing to a static HTML page.
     * This property is used only if [[enablePrettyUrl]] is `true`.
     */
    public $suffix;    

    private $_baseUrl;
    private $_scriptUrl;
    private $_hostInfo;
    /**
     * @var string the GET parameter name for route. This property is used only if [[enablePrettyUrl]] is `false`.
     */
    public $routeParam = 'app';    
    
    public function __construct($config = null) {

        $this->setOptions($config);
        
        return $this;
    }
    

    /**
     * 
     * @param type $rules
     * @param type $append
     * @return type
     */
    public function addRules($rules, $append = true)
    {
        if (!$this->getOption('enableSeoURL')) {
            return;
        }
        $rules = $this->buildRules($rules);
        if ($append) {
            $this->rules = array_merge($this->rules, $rules);
        } else {
            $this->rules = array_merge($rules, $this->rules);
        }
    }
    
    protected function buildRules($ruleDeclarations)
    {
        $builtRules = false; //todo cache
        
        if ($builtRules !== false) {
            return $builtRules;
        }
    //    dump($ruleDeclarations);
        
        $builtRules = [];
        $verbs = 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS';
        foreach ($ruleDeclarations as $key => $rule) {
            if (is_string($rule)) {
                $rule = ['route' => $rule];
                if (preg_match("/^((?:($verbs),)*($verbs))\\s+(.*)$/", $key, $matches)) {
                    $rule['verb'] = explode(',', $matches[1]);
                    // rules that are not applicable for GET requests should not be used to create URLs
                    if (!in_array('GET', $rule['verb'], true)) {
                        $rule['mode'] = Rule::PARSING_ONLY;
                    }
                    $key = $matches[4];
                }
                $rule['pattern'] = $key;
               
            }
            if (is_array($rule)) {
                $rule = new Rule($rule);
            }

            $builtRules[] = $rule;
        }
        return $builtRules;
    }    

    /**
     * 
     * @param Request $request
     * @return boolean
     */
    public function parseRequest(Request $request) {
//         if(\Enjoys\Core\Users::getInstance()->isAdmin()){
//                    dump($request);
//                }
        //dump($request->getQueryString());
        if ($this->getOption('enableSeoURL')) {
            /* @var $rule \Enjoys\Core\URL\Rule */
            foreach ($this->rules as $rule) {
                $result = $rule->parseRequest($this, $request);
               
                if ($result !== false) {
                    return $result;
                }
            }
            if ($this->getOption('enableStrictParsing')) {
                return false;
            }
          //  _var_dump('No matching URL rules. Using default URL parsing logic.', __METHOD__);
            $suffix = (string) $this->suffix;
            $pathInfo = $request->getPathInfo();

            if ($suffix !== '' && $pathInfo !== '') {
                $n = strlen($this->suffix);
                if (substr_compare($pathInfo, $this->suffix, -$n, $n) === 0) {
                    $pathInfo = substr($pathInfo, 0, -$n);
                    if ($pathInfo === '') {
                        // suffix alone is not allowed
                        return false;
                    }
                } else {
                    // suffix doesn't match
                    return false;
                }
            }
            return [$pathInfo, []];
        }
        //_var_dump('Pretty URL not enabled. Using default URL parsing logic.', __METHOD__);
        $route = $request->getQueryParam($this->routeParam, '');

        if (is_array($route)) {
            $route = '';
        }
        return [(string) $route, []];        
    }
    
    /**
     * Creates a URL using the given route and query parameters.
     *
     * You may specify the route as a string, e.g., `site/index`. You may also use an array
     * if you want to specify additional query parameters for the URL being created. The
     * array format must be:
     *
     * ```php
     * // generates: /index.php?r=site%2Findex&param1=value1&param2=value2
     * ['site/index', 'param1' => 'value1', 'param2' => 'value2']
     * ```
     *
     * If you want to create a URL with an anchor, you can use the array format with a `#` parameter.
     * For example,
     *
     * ```php
     * // generates: /index.php?r=site%2Findex&param1=value1#name
     * ['site/index', 'param1' => 'value1', '#' => 'name']
     * ```
     *
     * The URL created is a relative one. Use [[createAbsoluteUrl()]] to create an absolute URL.
     *
     * Note that unlike [[\yii\helpers\Url::toRoute()]], this method always treats the given route
     * as an absolute route.
     *
     * @param string|array $params use a string to represent a route (e.g. `site/index`),
     * or an array to represent a route with query parameters (e.g. `['site/index', 'param1' => 'value1']`).
     * @return string the created URL
     */
    public function createUrl($params)
    {
        $params = (array) $params;
       
        $anchor = isset($params['#']) ? '#' . $params['#'] : '';
        unset($params['#'], $params[$this->routeParam]);
        $route = trim($params[0], '/');
        unset($params[0]);
        $baseUrl = !$this->getOption('enableSeoURL') ? $this->getBaseUrl().'/' : $this->getBaseUrl();
        
        if ($this->getOption('enableSeoURL')) {
            $cacheKey = $route . '?';
            foreach ($params as $key => $value) {
                if ($value !== null) {
                    $cacheKey .= $key . '&';
                }
            }
            $url = $this->getUrlFromCache($cacheKey, $route, $params);
            if ($url === false) {
                /* @var $rule \Enjoys\Core\URL\Rule */
                foreach ($this->rules as $rule) {
                    if (in_array($rule, $this->_ruleCache[$cacheKey], true)) {
                        // avoid redundant calls of `UrlRule::createUrl()` for rules checked in `getUrlFromCache()`
                        // @see https://github.com/yiisoft/yii2/issues/14094
                        continue;
                    }
                    $url = $rule->createUrl($this, $route, $params);
//                    dump($params);
                    //dump($route);
                    if ($this->canBeCached($rule)) {
                        $this->setRuleToCache($cacheKey, $rule);
                    }
                    if ($url !== false) {
                        break;
                    }
                }
            }
            if ($url !== false) {
                if (strpos($url, '://') !== false) {
                    if ($baseUrl !== '' && ($pos = strpos($url, '/', 8)) !== false) {
                        return substr($url, 0, $pos) . $baseUrl . substr($url, $pos) . $anchor;
                    }
                    return $url . $baseUrl . $anchor;
                } elseif (strncmp($url, '//', 2) === 0) {
                    if ($baseUrl !== '' && ($pos = strpos($url, '/', 2)) !== false) {
                        return substr($url, 0, $pos) . $baseUrl . substr($url, $pos) . $anchor;
                    }
                    return $url . $baseUrl . $anchor;
                }
                $url = ltrim($url, '/');
                return "$baseUrl/{$url}{$anchor}";
            }
            if ($this->suffix !== null) {
                $route .= $this->suffix;
            }
            if (!empty($params) && ($query = http_build_query($params)) !== '') {
                $route .= '?' . $query;
            }
            $route = ltrim($route, '/');
            return "$baseUrl/{$route}{$anchor}";
        }
        $url = "$baseUrl?{$this->routeParam}=" . urlencode($route);
        if (!empty($params) && ($query = http_build_query($params)) !== '') {
            $url .= '&' . $query;
        }
        return $url . $anchor;
    }
    /**
     * Returns the value indicating whether result of [[createUrl()]] of rule should be cached in internal cache.
     *
     * @param UrlRuleInterface $rule
     * @return bool `true` if result should be cached, `false` if not.
     * @since 2.0.12
     * @see getUrlFromCache()
     * @see setRuleToCache()
     * @see UrlRule::getCreateUrlStatus()
     */
    protected function canBeCached(Rule $rule)
    {
        return
            // if rule does not provide info about create status, we cache it every time to prevent bugs like #13350
            // @see https://github.com/yiisoft/yii2/pull/13350#discussion_r114873476
            !method_exists($rule, 'getCreateUrlStatus') || ($status = $rule->getCreateUrlStatus()) === null
            || $status === Rule::CREATE_STATUS_SUCCESS
            || $status & Rule::CREATE_STATUS_PARAMS_MISMATCH;
    }
    
    protected function getUrlFromCache($cacheKey, $route, $params)
    {
        if (!empty($this->_ruleCache[$cacheKey])) {
            foreach ($this->_ruleCache[$cacheKey] as $rule) {
                /* @var $rule UrlRule */
                if (($url = $rule->createUrl($this, $route, $params)) !== false) {
                    return $url;
                }
            }
        } else {
            $this->_ruleCache[$cacheKey] = [];
        }
        return false;
    }
    /**
     * Store rule (e.g. [[UrlRule]]) to internal cache.
     * @param $cacheKey
     * @param UrlRuleInterface $rule
     * @since 2.0.8
     */
    protected function setRuleToCache($cacheKey, Rule $rule)
    {
        $this->_ruleCache[$cacheKey][] = $rule;
    }
    /**
     * Creates an absolute URL using the given route and query parameters.
     *
     * This method prepends the URL created by [[createUrl()]] with the [[hostInfo]].
     *
     * Note that unlike [[\yii\helpers\Url::toRoute()]], this method always treats the given route
     * as an absolute route.
     *
     * @param string|array $params use a string to represent a route (e.g. `site/index`),
     * or an array to represent a route with query parameters (e.g. `['site/index', 'param1' => 'value1']`).
     * @param string|null $scheme the scheme to use for the URL (either `http`, `https` or empty string
     * for protocol-relative URL).
     * If not specified the scheme of the current request will be used.
     * @return string the created URL
     * @see createUrl()
     */
    public function createAbsoluteUrl($params, $scheme = null)
    {
        $params = (array) $params;
        $url = $this->createUrl($params);
        if (strpos($url, '://') === false) {
            $hostInfo = $this->getHostInfo();
            if (strncmp($url, '//', 2) === 0) {
                $url = substr($hostInfo, 0, strpos($hostInfo, '://')) . ':' . $url;
            } else {
                $url = $hostInfo . $url;
            }
        }
        return \Enjoys\Helpers\URL::ensureScheme($url, $scheme);
    }
    
    /**
     * 
     * @return type
     * @throws Exception
     */
    public function getBaseUrl()
    {
        if ($this->_baseUrl === null) {
            $request = Request::getInstance();
            if ($request instanceof Request) {
                $this->_baseUrl = $request->getBaseUrl();
            } else {
                throw new Exception('Please configure UrlManager::baseUrl correctly as you are running a console application.');
            }
        }
        return $this->_baseUrl;
    }
    
    /**
     * 
     * @param type $value
     */
    public function setBaseUrl($value)
    {
        $this->_baseUrl = $value === null ? null : rtrim($value, '/');
    }
    
    /**
     * 
     * @return type
     * @throws Exception
     */
    public function getScriptUrl()
    {
        if ($this->_scriptUrl === null) {
            $request = Request::getInstance();
            if ($request instanceof Request) {
                $this->_scriptUrl = $request->getScriptUrl();
            } else {
                throw new Exception('Please configure UrlManager::scriptUrl correctly as you are running a console application.');
            }
        }
        return $this->_scriptUrl;
    }
    
    /**
     * 
     * @param type $value
     */
    public function setScriptUrl($value)
    {
        $this->_scriptUrl = $value;
    }
    
    /**
     * 
     * @return type
     * @throws Exception
     */
    public function getHostInfo()
    {
        if ($this->_hostInfo === null) {
            $request = Request::getInstance();
            if ($request instanceof Request) {
                $this->_hostInfo = $request->getHostInfo();
            } else {
                throw new Exception('Please configure UrlManager::hostInfo correctly as you are running a console application.');
            }
        }
        return $this->_hostInfo;
    }
    
    /**
     * 
     * @param type $value
     */
    public function setHostInfo($value)
    {
        $this->_hostInfo = $value === null ? null : rtrim($value, '/');
    }    
}
