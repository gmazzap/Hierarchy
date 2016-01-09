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
use GM\Hierarchy\Branch\BranchPage;
use GM\Hierarchy\Tests\TestCase;
use Mockery;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class BranchPageTest extends TestCase
{
    public function testLeavesNoPageNoPagename()
    {
        $post = Mockery::mock('\WP_Post');
        $post->ID = 0;
        $post->post_name = '';
        $post->post_type = '';

        $branch = new BranchPage();
        Functions::when('get_queried_object')->justReturn($post);
        Functions::expect('get_query_var')->with('pagename')->andReturn('');

        assertSame(['page', 'singular'], $branch->leaves());
    }

    public function testLeavesNoPage()
    {
        $post = Mockery::mock('\WP_Post');
        $post->ID = 0;
        $post->post_name = '';
        $post->post_type = '';

        Functions::when('get_queried_object')->justReturn($post);
        Functions::expect('get_query_var')->with('pagename')->andReturn('foo');
        Functions::expect('get_page_template_slug')->with($post)->andReturn('');

        $branch = new BranchPage();

        assertSame(['page-foo', 'page-0', 'page', 'singular'], $branch->leaves());
    }

    public function testLeavesPage()
    {
        $post = Mockery::mock('\WP_Post');
        $post->ID = 1;
        $post->post_name = 'foo';
        $post->post_type = 'page';

        Functions::when('get_queried_object')->justReturn($post);
        Functions::expect('get_query_var')->with('pagename')->andReturn('');
        Functions::expect('get_page_template_slug')->with($post)->andReturn('');

        $branch = new BranchPage();
        assertSame(['page-foo', 'page-1', 'page', 'singular'], $branch->leaves());
    }

    public function testLeavesPagePagename()
    {
        $post = Mockery::mock('\WP_Post');
        $post->ID = 1;
        $post->post_name = 'foo';
        $post->post_type = 'page';

        Functions::when('get_queried_object')->justReturn($post);
        Functions::expect('get_query_var')->with('pagename')->andReturn('bar');
        Functions::expect('get_page_template_slug')->with($post)->andReturn('');

        $branch = new BranchPage();

        assertSame(['page-bar', 'page-1', 'page', 'singular'], $branch->leaves());
    }

    public function testLeavesPagePagenameTemplate()
    {
        $post = Mockery::mock('\WP_Post');
        $post->ID = 1;
        $post->post_name = 'foo';
        $post->post_type = 'page';

        Functions::when('get_queried_object')->justReturn($post);
        Functions::expect('get_query_var')->with('pagename')->andReturn('bar');
        Functions::expect('get_page_template_slug')->with($post)->andReturn('page-meh.php');
        Functions::expect('validate_file')->with('page-meh.php')->andReturn(0);

        $branch = new BranchPage();

        assertSame(['page-meh', 'page-bar', 'page-1', 'page', 'singular'], $branch->leaves());
    }

    public function testLeavesPagePagenameTemplateFolder()
    {
        $post = Mockery::mock('\WP_Post');
        $post->ID = 1;
        $post->post_name = 'foo';
        $post->post_type = 'page';

        Functions::when('get_queried_object')->justReturn($post);
        Functions::expect('get_query_var')->with('pagename')->andReturn('bar');
        Functions::expect('get_page_template_slug')->with($post)->andReturn('page-templates/page-meh.php');
        Functions::expect('validate_file')->with('page-templates/page-meh.php')->andReturn(0);

        $branch = new BranchPage();

        assertSame(['page-templates/page-meh', 'page-bar', 'page-1', 'page', 'singular'],
            $branch->leaves());
    }

    public function testLeavesPagePagenameTemplateNoValidate()
    {
        $post = Mockery::mock('\WP_Post');
        $post->ID = 1;
        $post->post_name = 'foo';
        $post->post_type = 'page';

        Functions::when('get_queried_object')->justReturn($post);
        Functions::expect('get_query_var')->with('pagename')->andReturn('bar');
        Functions::expect('get_page_template_slug')->with($post)->andReturn('page-meh.php');
        Functions::expect('validate_file')->with('page-meh.php')->andReturn(1);

        $branch = new BranchPage();

        assertSame(['page-bar', 'page-1', 'page', 'singular'], $branch->leaves());
    }
}
