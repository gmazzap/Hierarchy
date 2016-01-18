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
        $query = new \WP_Query();
        $branch = new BranchTag();

        assertSame(['tag'], $branch->leaves($query));
    }

    public function testLeaves()
    {
        $tag = (object) ['slug' => 'foo', 'term_id' => 123];
        $query = new \WP_Query([], $tag);

        $branch = new BranchTag();
        assertSame(['tag-foo', 'tag-123', 'tag'], $branch->leaves($query));
    }
}
