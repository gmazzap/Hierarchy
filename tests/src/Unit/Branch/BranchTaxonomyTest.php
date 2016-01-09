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

use GM\Hierarchy\Branch\BranchTaxonomy;
use GM\Hierarchy\Tests\TestCase;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class BranchTaxonomyTest extends TestCase
{
    public function testLeavesNoTax()
    {
        $query = new \WP_Query([]);
        $branch = new BranchTaxonomy();

        assertSame(['taxonomy'], $branch->leaves($query));
    }

    public function testLeaves()
    {
        $taxonomy = (object) ['slug' => 'foo', 'taxonomy' => 'custom-tax'];
        $query = new \WP_Query([], $taxonomy);

        $branch = new BranchTaxonomy();
        $expected = ['taxonomy-custom-tax-foo', 'taxonomy-custom-tax', 'taxonomy'];
        assertSame($expected, $branch->leaves($query));
    }
}
