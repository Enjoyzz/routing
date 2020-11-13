<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Enjoys\Route\Url;

/**
 * Class ParseRequest
 *
 * @author Enjoys
 */
class Parse
{

//    private \Enjoys\Route\Manager $manager;
//    private \Enjoys\Route\Request\RequestInterface $request;

    private ?array $result = null;

    private const BASE_CLASS = Parse\BaseParse::class;
    private const RULE_CLASS = Parse\RuleParse::class;

    public function __construct(\Enjoys\Route\Request\RequestInterface $request, \Enjoys\Route\Manager $manager)
    {


        $class = self::BASE_CLASS;

        if ($manager->getOption('prettyUrl')) {
            $class = self::RULE_CLASS;
        }

        $this->result = (new $class($request, $manager))->parse();

        $request->query->add($this->result['params']);
    }

    public function getRoute()
    {
        return $this->result['route'];
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getParams()
    {
        return $this->result['params'];
    }
}
