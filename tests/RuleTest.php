<?php

declare(strict_types=1);

namespace Tests\Enjoys\Route;

/**
 * Description of RuleTest
 *
 * @author Enjoys
 */
class RuleTest extends \PHPUnit\Framework\TestCase
{
    public function test_no_pattern()
    {
        $this->expectException(\Enjoys\Route\Exception\ConfigRuleException::class);
        new \Enjoys\Route\Rule([]);
        
    }
    public function test_no_route()
    {
        $this->expectException(\Enjoys\Route\Exception\ConfigRuleException::class);
        new \Enjoys\Route\Rule(['pattern' => 'pattern']);
        
    }
    public function test_no_name()
    {
        $rule = new \Enjoys\Route\Rule(['pattern' => 'mypattern', 'route' => 'myroute']);
        $this->assertSame('mypattern', $rule->name);
    }
    public function test_isset_name()
    {
        $rule = new \Enjoys\Route\Rule(['name' => 'myname', 'pattern' => 'mypattern', 'route' => 'myroute']);
        $this->assertSame('myname', $rule->name);
    }
}
