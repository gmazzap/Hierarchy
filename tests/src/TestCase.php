<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\Hierarchy\Tests;

use Andrew\StaticProxy;
use PHPUnit_Framework_TestCase;
use Brain\Monkey;
use Andrew\Proxy;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
class TestCase extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        Monkey::setUpWP();
        Monkey\Functions::when('trailingslashit')->alias(function ($str) {
            return rtrim($str, '\\/').'/';
        });
    }

    protected function tearDown()
    {
        Monkey::tearDownWP();
        parent::tearDown();
    }

    /**
     * @param string $var
     * @param mixed  $value
     * @param object $object
     */
    protected function setPrivateVar($var, $value, $object)
    {
        $proxy = new Proxy($object);
        $proxy->{$var} = $value;
    }

    /**
     * @param string $var
     * @param object $object
     * @return mixed
     */
    protected function getPrivateVar($var, $object)
    {
        $proxy = new Proxy($object);

        return $proxy->{$var};
    }

    /**
     * @param string $var
     * @param object $object
     * @return mixed
     */
    protected function getPrivateStaticVar($var, $object)
    {
        $proxy = new StaticProxy(get_class($object));

        return $proxy->{$var};
    }

    /**
     * @param string $method
     * @param object $object
     * @param array  $args
     * @return mixed
     */
    protected function callPrivateFunc($method, $object, $args = [])
    {
        return call_user_func_array([new Proxy($object), $method], $args);
    }

}