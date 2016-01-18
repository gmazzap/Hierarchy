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

use GM\Hierarchy\Branch\BranchSingle;
use GM\Hierarchy\Tests\TestCase;
use Mockery;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class BranchSingleTest extends TestCase
{
    public function testLeavesNoPost()
    {
        $post = Mockery::mock('\WP_Post');
        $post->ID = 0;
        $post->post_name = '';
        $query = new \WP_Query([], $post);

        $branch = new BranchSingle();

        assertSame(['single', 'singular'], $branch->leaves($query));
    }

    public function testLeaves()
    {
        $post = Mockery::mock('\WP_Post');
        $post->ID = 123;
        $post->post_type = 'my_cpt';
        $query = new \WP_Query([], $post);

        $branch = new BranchSingle();

        assertSame(['single-my_cpt', 'single', 'singular'], $branch->leaves($query));
    }
}
