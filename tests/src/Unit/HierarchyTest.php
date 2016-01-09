<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\Hierarchy\Tests\Unit;

use GM\Hierarchy\Tests\TestCase;
use GM\Hierarchy\Tests\Stubs;
use GM\Hierarchy\Branch\BranchInterface;
use GM\Hierarchy\Hierarchy;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
class HierarchyTest extends TestCase
{
    public function testParse()
    {
        $query = new \WP_Query();
        $hierarchy = new Hierarchy($query);

        $branches = [
            Stubs\BranchStubFoo::class,
            Stubs\BranchStubBar::class,
            Stubs\BranchStubBar2::class,
            Stubs\BranchStubBaz::class,
        ];

        $this->callPrivateFunc('parse', $hierarchy, [$branches]);

        $expected = [
            'foo'   => ['foo', 'bar'],
            'bar'   => ['baz', 'bar'],
            'index' => ['index'],
        ];

        $expectedFlat = [
            'foo',
            'bar',
            'baz',
            'index',
        ];

        assertSame($expected, $hierarchy->get());
        assertSame($expectedFlat, $hierarchy->getHierarchyFlat());
    }

    public function testBranches()
    {
        $hierarchy = new Hierarchy();
        $classes = $this->getPrivateStaticVar('branches', $hierarchy);

        foreach ($classes as $class) {
            assertInstanceOf(BranchInterface::class, new $class());
        }
    }
}
