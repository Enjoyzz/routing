<?php

namespace Enjoys\Route;

class Rule
{

    /**
     * Set [[mode]] with this value to mark that this rule is for URL parsing only.
     */
    const PARSING_ONLY = 1;

    /**
     * Set [[mode]] with this value to mark that this rule is for URL creation only.
     */
    const CREATION_ONLY = 2;

    /**
     * Represents the successful URL generation by last [[createUrl()]] call.
     * @see $createStatus
     * @since 2.0.12
     */
    const CREATE_STATUS_SUCCESS = 0;

    /**
     * Represents the unsuccessful URL generation by last [[createUrl()]] call, because rule does not support
     * creating URLs.
     * @see $createStatus
     * @since 2.0.12
     */
    const CREATE_STATUS_PARSING_ONLY = 1;

    /**
     * Represents the unsuccessful URL generation by last [[createUrl()]] call, because of mismatched route.
     * @see $createStatus
     * @since 2.0.12
     */
    const CREATE_STATUS_ROUTE_MISMATCH = 2;

    /**
     * Represents the unsuccessful URL generation by last [[createUrl()]] call, because of mismatched
     * or missing parameters.
     * @see $createStatus
     * @since 2.0.12
     */
    const CREATE_STATUS_PARAMS_MISMATCH = 4;

    /**
     * @var string the name of this rule. If not set, it will use [[pattern]] as the name.
     */
    public $name;

    /**
     * On the rule initialization, the [[pattern]] matching parameters names will be replaced with [[placeholders]].
     * @var string the pattern used to parse and create the path info part of a URL.
     * @see host
     * @see placeholders
     */
    public $pattern;

    /**
     * @var string the pattern used to parse and create the host info part of a URL (e.g. `http://example.com`).
     * @see pattern
     */
    public $host;

    /**
     * @var string the route to the controller action
     */
    public $route;

    /**
     * @var array the default GET parameters (name => value) that this rule provides.
     * When this rule is used to parse the incoming request, the values declared in this property
     * will be injected into $_GET.
     */
    public $defaults = [];

    /**
     * @var string the URL suffix used for this rule.
     * For example, ".html" can be used so that the URL looks like pointing to a static HTML page.
     * If not set, the value of [[UrlManager::suffix]] will be used.
     */
    public $suffix = '';

    /**
     * @var string|array the HTTP verb (e.g. GET, POST, DELETE) that this rule should match.
     * Use array to represent multiple verbs that this rule may match.
     * If this property is not set, the rule can match any verb.
     * Note that this property is only used when parsing a request. It is ignored for URL creation.
     */
    public $verb;

    /**
     * @var int a value indicating if this rule should be used for both request parsing and URL creation,
     * parsing only, or creation only.
     * If not set or 0, it means the rule is both request parsing and URL creation.
     * If it is [[PARSING_ONLY]], the rule is for request parsing only.
     * If it is [[CREATION_ONLY]], the rule is for URL creation only.
     */
    public $mode;

    /**
     * @var bool a value indicating if parameters should be url encoded.
     */
    public $encodeParams = true;

    /**
     * @var UrlNormalizer|array|false|null the configuration for [[UrlNormalizer]] used by this rule.
     * If `null`, [[UrlManager::normalizer]] will be used, if `false`, normalization will be skipped
     * for this rule.
     * @since 2.0.10
     */
    public $normalizer;

    /**
     * @var int|null status of the URL creation after the last [[createUrl()]] call.
     * @since 2.0.12
     */
    protected $createStatus;

    /**
     * @var array list of placeholders for matching parameters names. Used in [[parseRequest()]], [[createUrl()]].
     * On the rule initialization, the [[pattern]] parameters names will be replaced with placeholders.
     * This array contains relations between the original parameters names and their placeholders.
     * The array keys are the placeholders and the values are the original names.
     *
     * @see parseRequest()
     * @see createUrl()
     * @since 2.0.7
     */
    public $placeholders = [];

    /**
     * @var string the template for generating a new URL. This is derived from [[pattern]] and is used in generating URL.
     */
    public $_template;

    /**
     * @var string the regex for matching the route part. This is used in generating URL.
     */
    public $_routeRule;

    /**
     * @var array list of regex for matching parameters. This is used in generating URL.
     */
    public $_paramRules = [];

    /**
     * @var array list of parameters used in the route.
     */
    public $_routeParams = [];

    public function __construct($config)
    {
        foreach ($config as $name => $value) {
            $this->$name = $value;
        }
     
        // dump($config);
        if ($this->pattern === null) {
            throw new ConfigRuleException('UrlRule::pattern must be set.');
        }
        if ($this->route === null) {
            throw new ConfigRuleException('UrlRule::route must be set.');
        }

        if ($this->verb !== null) {
            if (is_array($this->verb)) {
                foreach ($this->verb as $i => $verb) {
                    $this->verb[$i] = strtoupper($verb);
                }
            } else {
                $this->verb = [strtoupper($this->verb)];
            }
        }
        if ($this->name === null) {
            $this->name = $this->pattern;
        }
        $this->preparePattern();
       
        $this->translatePattern(true);
    }

    /**
     * Process [[$pattern]] on rule initialization.
     */
    private function preparePattern()
    {
        $this->pattern = \Enjoys\Route\Helpers::trimSlashes($this->pattern);
        $this->route = trim($this->route, '/');
        if ($this->host !== null) {
            $this->host = rtrim($this->host, '/');
            $this->pattern = rtrim($this->host . '/' . $this->pattern, '/');
//        } elseif ($this->pattern === '') {
//            $this->_template = '';
//            //$this->pattern = '#^$#u';
//            return;
        } elseif (($pos = strpos($this->pattern, '://')) !== false) {
            if (($pos2 = strpos($this->pattern, '/', $pos + 3)) !== false) {
                $this->host = substr($this->pattern, 0, $pos2);
            } else {
                $this->host = $this->pattern;
            }
        } elseif (strncmp($this->pattern, '//', 2) === 0) {
            if (($pos2 = strpos($this->pattern, '/', 2)) !== false) {
                $this->host = substr($this->pattern, 0, $pos2);
            } else {
                $this->host = $this->pattern;
            }
        } else {
            $this->pattern = '/' . $this->pattern . '/';
        }
       // \Enjoys\dump( $this->pattern);
//
        if (strpos($this->route, '<') !== false && preg_match_all('/<([\w._-]+)>/', $this->route, $matches)) {
            foreach ($matches[1] as $name) {
                $this->_routeParams[$name] = "<$name>";
            }
        }
        
       
       
    }

    /**
     * Prepares [[$pattern]] on rule initialization - replace parameter names by placeholders.
     *
     * @param bool $allowAppendSlash Defines position of slash in the param pattern in [[$pattern]].
     * If `false` slash will be placed at the beginning of param pattern. If `true` slash position will be detected
     * depending on non-optional pattern part.
     */
    private function translatePattern($allowAppendSlash)
    {
        $tr = [
            '.' => '\\.',
            '*' => '\\*',
            '$' => '\\$',
            '[' => '\\[',
            ']' => '\\]',
            '(' => '\\(',
            ')' => '\\)',
        ];
        $tr2 = [];
        $requiredPatternPart = $this->pattern;
        $oldOffset = 0;

       
        if (preg_match_all('/<([\w._-]+):?([^>]+)?>/', $this->pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {

            $appendSlash = false;
            foreach ($matches as $match) {
                //dump($match);
                $name = $match[1][0];
                $pattern = isset($match[2][0]) ? $match[2][0] : '[^\/]+';
                $placeholder = 'a' . hash('crc32b', $name); // placeholder must begin with a letter
                $this->placeholders[$placeholder] = $name;
                if (array_key_exists($name, $this->defaults)) {
                    $length = strlen($match[0][0]);
                    $offset = $match[0][1];
                    $requiredPatternPart = str_replace("/{$match[0][0]}/", '//', $requiredPatternPart);
                    if (
                            $allowAppendSlash && ($appendSlash || $offset === 1) &&
                            (($offset - $oldOffset) === 1) &&
                            isset($this->pattern[$offset + $length]) &&
                            $this->pattern[$offset + $length] === '/' &&
                            isset($this->pattern[$offset + $length + 1])
                    ) {
                        // if pattern starts from optional params, put slash at the end of param pattern
                        // @see https://github.com/yiisoft/yii2/issues/13086
                        $appendSlash = true;
                        $tr["<$name>/"] = "((?P<$placeholder>$pattern)/)?";
                    } elseif (
                            $offset > 1 && $this->pattern[$offset - 1] === '/' && 
                            (!isset($this->pattern[$offset + $length]) || $this->pattern[$offset + $length] === '/')
                    ) {
                        $appendSlash = false;
                        $tr["/<$name>"] = "(/(?P<$placeholder>$pattern))?";
                    }
                    $tr["<$name>"] = "(?P<$placeholder>$pattern)?";
                    $oldOffset = $offset + $length;
                } else {
                    $appendSlash = false;
                    $tr["<$name>"] = "(?P<$placeholder>$pattern)";
                }


                /* enjoys */

                $this->_paramRules[$name] = $pattern === '[^\/]+' ? '' : "#^$pattern$#u";
     
                if (isset($this->_routeParams[$name])) {
                    $tr2["<$name>"] = "(?P<$placeholder>$pattern)";
                }

                /* \enjoys */
//                if (isset($this->_routeParams[$name])) {
//                    $tr2["<$name>"] = "(?P<$placeholder>$pattern)";
//                } else {
//                    $this->_paramRules[$name] = $pattern === '[^\/]+' ? '' : "#^$pattern$#u";
//                }
            }
        }


        // we have only optional params in route - ensure slash position on param patterns
        if ($allowAppendSlash && trim($requiredPatternPart, '/') === '') {
            $this->translatePattern(false);
            return;
        }
       
        
        
        $this->_template = preg_replace('/<([\w._-]+):?([^>]+)?>/', '<$1>', $this->pattern);
        
         
         
        $this->pattern = '#^' . trim(strtr($this->_template, $tr), '/') . '$#u';
        // if host starts with relative scheme, then insert pattern to match any
        if (strncmp($this->host, '//', 2) === 0) {
            $this->pattern = substr_replace($this->pattern, '[\w]+://', 2, 0);
        }
        if (!empty($this->_routeParams)) {
            $this->_routeRule = '#^' . strtr($this->route, $tr2) . '$#u';
        }
    }

   
    /**
     * Returns list of regex for matching parameter.
     * @return array parameter keys and regexp rules.
     *
     * @since 2.0.6
     */
    protected function getParamRules()
    {
        return $this->_paramRules;
    }

    /**
     * Iterates over [[placeholders]] and checks whether each placeholder exists as a key in $matches array.
     * When found - replaces this placeholder key with a appropriate name of matching parameter.
     * Used in [[parseRequest()]], [[createUrl()]].
     *
     * @param array $matches result of `preg_match()` call
     * @return array input array with replaced placeholder keys
     * @see placeholders
     * @since 2.0.7
     */
    protected function substitutePlaceholderNames(array $matches)
    {
        foreach ($this->placeholders as $placeholder => $name) {
            if (isset($matches[$placeholder])) {
                $matches[$name] = $matches[$placeholder];
                unset($matches[$placeholder]);
            }
        }
        return $matches;
    }


}
