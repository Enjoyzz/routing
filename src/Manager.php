<?php

namespace Enjoys\Route;

class Manager
{
    use \Enjoys\Traits\Options;

    private array $rules = [];

    /**
     * @var string the URL suffix used when [[enablePrettyUrl]] is `true`.
     * For example, ".html" can be used so that the URL looks like pointing to a static HTML page.
     * This property is used only if [[enablePrettyUrl]] is `true`.
     */
    private string $suffix = '';
    private string $baseUrl = '/';
    private ?string $scriptUrl = null;
    private ?string $hostInfo = null;

    /**
     * @var string the GET parameter name for route. This property is used only if [[enablePrettyUrl]] is `false`.
     */
    public string $routeParam = 'route';

    public function __construct(array $config = [])
    {
        $this->setOptions($config);
    }

    public function setSuffix(string $suffix = ''): void
    {
        
        $this->suffix = $suffix;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    public function setRouteParam(string $param): void
    {
        $this->routeParam = $param;
    }

    public function getRouteParam(): string
    {
        return $this->routeParam;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function addRules(array $rules, bool $append = true): void
    {
        $buildedRules = $this->buildRules($rules);
        if ($append) {
            $this->rules = array_merge($this->rules, $buildedRules);
        } else {
            $this->rules = array_merge($buildedRules, $this->rules);
        }
        // \Enjoys\dump($this->rules);
    }

    protected function buildRules(array $ruleDeclarations): array
    {

        $builtRules = [];
        //$verbs = 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS';
        foreach ($ruleDeclarations as $key => $rule) {
            //            if (is_string($rule)) {
            //                $rule = ['route' => $rule];
            //                if (preg_match("/^((?:($verbs),)*($verbs))\\s+(.*)$/", $key, $matches)) {
            //                    $rule['verb'] = explode(',', $matches[1]);
            //                    // rules that are not applicable for GET requests should not be used to create URLs
            //                    if (!in_array('GET', $rule['verb'], true)) {
            //                        $rule['mode'] = Rule::PARSING_ONLY;
            //                    }
            //                    $key = $matches[4];
            //                }
            //                $rule['pattern'] = $key;
            //               
            //            }
            if (is_array($rule)) {
                $rule = new Rule($rule);
            }

            $builtRules[] = $rule;
        }
        return $builtRules;
    }

    /**
     * 
     * @return string
     * @throws Exception\ManagerException
     */
    public function getBaseUrl(): string
    {

        return $this->baseUrl;
    }

    /**
     * 
     * @param string $value
     */
    public function setBaseUrl(string $value): void
    {
        $baseUrl = $value;
        
        if (strpos($value, '/', -1) === false) {
            $baseUrl = $baseUrl . '/';
        }

        if (strpos($value, '/', 0) === false) {
            $baseUrl = '/' . $baseUrl;
        }

        $this->baseUrl = $baseUrl;
    }

    /**
     * 
     * @return string
     * @throws Exception\ManagerException
     */
    public function getScriptUrl(): string
    {
        if ($this->scriptUrl === null) {

            throw new Exception\ManagerException('Please configure UrlManager::scriptUrl correctly as you are running a console application.');
        }
        return $this->scriptUrl;
    }

    /**
     * 
     * @param string $value
     */
    public function setScriptUrl(string $value): void
    {
        $this->scriptUrl = $value;
    }

    /**
     * 
     * @return string
     * @throws Exception\ManagerException
     */
    public function getHostInfo(): string
    {
        if ($this->hostInfo === null) {
            throw new Exception\ManagerException('Please configure UrlManager::hostInfo correctly as you are running a console application.');
        }
        return $this->hostInfo;
    }

    /**
     * 
     * @param string $value
     */
    public function setHostInfo(string $value): void
    {
        $this->hostInfo = rtrim($value, '/');
    }
}
