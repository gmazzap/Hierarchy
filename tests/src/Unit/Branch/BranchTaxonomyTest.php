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
use GM\Hierarchy\Branch\BranchTaxonomy;
use GM\Hierarchy\Tests\TestCase;
use Mockery;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class BranchTaxonomyTest extends TestCase
{

    public function testLeavesNoTax()
    {
        Functions::when('get_queried_object')->justReturn();
        $branch = new BranchTaxonomy();

        assertSame(['taxonomy'], $branch->leaves());
    }

    public function testLeaves()
    {
        $taxonomy = (object)['slug' => 'foo', 'taxonomy' => 'custom-tax'];
        Functions::when('get_queried_object')->justReturn($taxonomy);

        $branch = new BranchTaxonomy();
        $expected = ['taxonomy-custom-tax-foo', 'taxonomy-custom-tax', 'taxonomy'];
        assertSame($expected, $branch->leaves());
    }
}