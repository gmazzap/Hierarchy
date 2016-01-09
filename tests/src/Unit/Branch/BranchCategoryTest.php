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
use GM\Hierarchy\Branch\BranchCategory;
use GM\Hierarchy\Tests\TestCase;
use Mockery;


/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class BranchCategoryTest extends TestCase
{

    public function testLeavesNoCategory()
    {
        Functions::when('get_queried_object')->justReturn();
        $branch = new BranchCategory();

        assertSame(['category'], $branch->leaves());
    }

    public function testLeaves()
    {
        $category = (object)['slug' => 'foo', 'term_id' => 123];
        Functions::when('get_queried_object')->justReturn($category);

        $branch = new BranchCategory();
        assertSame(['category-foo', 'category-123', 'category'], $branch->leaves());
    }
}