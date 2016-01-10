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
        $hierarchy = new Hierarchy();

        $branches = [
            Stubs\BranchStubFoo::class,  // leaves: ['foo', 'bar']
            Stubs\BranchStubBar::class,  // leaves: ['baz', 'bar']
            Stubs\BranchStubBar2::class, // should be skipped because has same name of previos
            Stubs\BranchStubBaz::class,  // should be skipped because its is() always returns false
        ];

        $this->setPrivateStaticVar('branches', $branches, $hierarchy);

        $query = new \WP_Query();
        /** @var \stdClass $data */
        $data = $this->callPrivateFunc('parse', $hierarchy, [$query]);

        $this->setPrivateStaticVar('branches', [], $hierarchy);

        $expected = [
            'foo'   => (new Stubs\BranchStubFoo())->leaves($query),
            'bar'   => (new Stubs\BranchStubBar())->leaves($query),
            'index' => ['index'],
        ];

        $expectedFlat = [
            'foo',
            'bar',
            'baz',
            'index',
        ];

        assertInstanceOf(\stdClass::class, $data);
        assertSame($expected, $data->hierarchy);
        assertSame($expectedFlat, $data->templates);
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
