<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\Hierarchy\Tests\Unit\Branch;

use Brain\Monkey\Functions;
use GM\Hierarchy\Branch\BranchTag;
use GM\Hierarchy\Tests\TestCase;


/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class BranchTagTest extends TestCase
{
    public function testLeavesNoTag()
    {
        Functions::when('get_queried_object')->justReturn();
        $branch = new BranchTag();

        assertSame(['tag'], $branch->leaves());
    }

    public function testLeaves()
    {
        $tag = (object) ['slug' => 'foo', 'term_id' => 123];
        Functions::when('get_queried_object')->justReturn($tag);

        $branch = new BranchTag();
        assertSame(['tag-foo', 'tag-123', 'tag'], $branch->leaves());
    }
}
