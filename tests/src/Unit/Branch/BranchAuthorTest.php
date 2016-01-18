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

use GM\Hierarchy\Branch\BranchAuthor;
use GM\Hierarchy\Tests\TestCase;
use Mockery;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class BranchAuthorTest extends TestCase
{
    public function testLeavesNoUser()
    {
        $branch = new BranchAuthor();
        $query = new \WP_Query([], null);

        assertSame(['author'], $branch->leaves($query));
    }

    public function testLeaves()
    {
        $user = Mockery::mock('\WP_User');
        $user->ID = 12;
        $user->user_nicename = 'john_doe';
        $query = new \WP_Query([], $user);

        $branch = new BranchAuthor();

        assertSame(['author-john_doe', 'author-12', 'author'], $branch->leaves($query));
    }
}
