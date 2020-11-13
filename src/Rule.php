<?php

namespace Enjoys\Route;

class Rule
{

    /**
     * Set [[mode]] with this value to mark that this rule is for URL parsing and creation.
     */
    const DEFAULT_MODE = 0;

    /**
     * Set [[mode]] with this value to mark that this rule is for URL parsing only.
     */
    const PARSING_ONLY = 1;

    /**
     * Set [[mode]] with this value to mark that this rule is for URL creation only.
     */
    const CREATION_ONLY = 2;

    public ?string $name = null;
    public ?string $pattern = null;
    public ?string $host = null;
    public ?string $route = null;
    public array $defaults = [];
    public ?string $suffix = null;

    /**
     * @var string|non-empty-array<array-key, mixed>|null
     */
    public $verb = null;
    public bool $encodeParams = true;
    public array $placeholders = [];
    public string $template;
    public ?string $ruleRoute = null;
    public array $ruleParams = [];

    /**
     * @var array list of parameters used in the route.
     */
    public array $routeParams = [];
    public array $callback = [];
    public int $mode = self::DEFAULT_MODE;

    public function __construct($config)
    {
        foreach ($config as $name => $value) {
            $this->$name = $value;
        }

        // dump($config);
        if ($this->pattern === null) {
            throw new Exception\ConfigRuleException('UrlRule::pattern must be set.');
        }
        if ($this->route === null) {
            throw new Exception\ConfigRuleException('UrlRule::route must be set.');
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
                $this->routeParams[$name] = "<$name>";
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

                $this->ruleParams[$name] = $pattern === '[^\/]+' ? '' : "#^$pattern$#u";

                if (isset($this->routeParams[$name])) {
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



        $this->template = preg_replace('/<([\w._-]+):?([^>]+)?>/', '<$1>', $this->pattern);



        $this->pattern = '#^' . trim(strtr($this->template, $tr), '/') . '$#u';
        // if host starts with relative scheme, then insert pattern to match any
        if (strncmp($this->host, '//', 2) === 0) {
            $this->pattern = substr_replace($this->pattern, '[\w]+://', 2, 0);
        }
        if (!empty($this->routeParams)) {
            $this->ruleRoute = '#^' . strtr($this->route, $tr2) . '$#u';
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
        return $this->ruleParams;
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
