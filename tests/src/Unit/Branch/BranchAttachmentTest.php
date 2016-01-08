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

use GM\Hierarchy\Branch\BranchAttachment;
use GM\Hierarchy\Tests\TestCase;
use Mockery;


/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class BranchAttachmentTest extends TestCase
{

    public function testLeavesNoPost()
    {
        $branch = new BranchAttachment();

        assertSame(['attachment'], $branch->leaves());
    }

    public function testLeaves()
    {
        $branch = new BranchAttachment();
        $post = Mockery::mock('\WP_Post');
        $post->post_mime_type = 'image/jpeg';

        $this->setPrivateVar('post', $post, $branch);

        assertSame(['image', 'jpeg', 'image_jpeg', 'attachment'], $branch->leaves());
    }
}