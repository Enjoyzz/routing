<?php

declare(strict_types=1);

namespace Tests\Enjoys\Route;

/**
 * Description of ManagerTest
 *
 * @author Enjoys
 */
class ManagerTest extends \PHPUnit\Framework\TestCase
{

    public function test_1()
    {
        $manager = new \Enjoys\Route\Manager();
        $manager->setSuffix('.html');
        $this->assertSame('.html', $manager->getSuffix());
//        $manager->setScriptUrl('index.php');
//        $this->assertSame('index.php', $manager->getScriptUrl());
    }

    public function test_getHostInfo()
    {
        $this->expectException(\Enjoys\Route\Exception\ManagerException::class);
        $manager = new \Enjoys\Route\Manager();
        $manager->getHostInfo();
    }
//
//    public function test_getScriptUrl()
//    {
//        $this->expectException(\Enjoys\Route\Exception\ManagerException::class);
//        $manager = new \Enjoys\Route\Manager();
//        $manager->getScriptUrl();
//    }

    public function test_addRules()
    {

        $manager = new \Enjoys\Route\Manager();
        $manager->addRules([1]);
        $manager->addRules([2]);
        $this->assertSame([1, 2], $manager->getRules());
    }
    public function test_addRules_append()
    {

        $manager = new \Enjoys\Route\Manager();
        $manager->addRules([1], false);
        $manager->addRules([2], false);
        $this->assertSame([2, 1], $manager->getRules());
    }
}
