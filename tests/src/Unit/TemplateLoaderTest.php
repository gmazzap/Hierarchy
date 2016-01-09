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

use Brain\Monkey\Functions;
use Brain\Monkey\WP\Actions;
use Brain\Monkey\WP\Filters;
use GM\Hierarchy\TemplateLoader;
use GM\Hierarchy\Tests\TestCase;
use GM\Hierarchy\Finder\FinderInterface;
use Mockery;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
class TemplateLoaderTest extends TestCase
{
    public function testFoundIsCached()
    {
        $finder = Mockery::mock(FinderInterface::class);

        $loader = new TemplateLoader($finder);
        $this->setPrivateVar('found', 'xxx', $loader);

        assertSame('xxx', $loader->find());
    }

    public function testFindEmptyWhenNoLeaves()
    {
        $finder = Mockery::mock(FinderInterface::class);
        $GLOBALS['wp_query'] = '';
        $loader = new TemplateLoader($finder);
        $found = $loader->find();
        unset($GLOBALS['wp_query']);

        assertSame('', $found);
    }

    public function testFindNoFilters()
    {
        $wpQuery = new \WP_Query();
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';
        $finder = Mockery::mock(FinderInterface::class);
        $finder->shouldReceive('findFirst')->once()->with(['index'], 'index')->andReturn($template);

        $loader = new TemplateLoader($finder);

        assertSame($template, $loader->find($wpQuery, false));
    }

    public function testFindFilters()
    {
        Filters::expectApplied('index_template')->once()->andReturn('foo.php');

        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';
        $wpQuery = new \WP_Query();
        $finder = Mockery::mock(FinderInterface::class);
        $finder->shouldReceive('findFirst')->once()->with(['index'], 'index')->andReturn($template);

        $loader = new TemplateLoader($finder);

        assertSame('foo.php', $loader->find($wpQuery, true));
    }

    public function testLoadNoFilters()
    {
        $wpQuery = new \WP_Query();
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';
        $finder = Mockery::mock(FinderInterface::class);
        $finder->shouldReceive('findFirst')->once()->with(['index'], 'index')->andReturn($template);

        $loader = new TemplateLoader($finder);

        ob_start();
        $loader->load($wpQuery, false);

        assertSame('index', trim(ob_get_clean()));
    }

    public function testLoadFilters()
    {
        $wpQuery = new \WP_Query();
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/another.php';

        Filters::expectApplied('template_include')->once()->andReturn($template);

        $finder = Mockery::mock(FinderInterface::class);
        $finder->shouldReceive('findFirst')->once()->with(['index'], 'index')->andReturn('foo');

        $loader = new TemplateLoader($finder);

        ob_start();
        $loader->load($wpQuery, true);

        assertSame('another', trim(ob_get_clean()));
    }

    public function testLoadAndExit()
    {
        Functions::expect('has_action')->once()->with(TemplateLoader::class.'.exit')->andReturn(false);

        $exit = false;
        Actions::expectFired(TemplateLoader::class.'.exit')->once()->whenHappen(function () use (
            &$exit
        ) {
            $exit = true;
        });

        $wpQuery = new \WP_Query();
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';

        $finder = Mockery::mock(FinderInterface::class);
        $finder->shouldReceive('findFirst')->once()->with(['index'], 'index')->andReturn($template);

        $loader = new TemplateLoader($finder);

        ob_start();
        $loader->load($wpQuery, true, true);

        assertSame('index', trim(ob_get_clean()));

        assertTrue($exit);
    }

    public function testLoadAndExitWithHelper()
    {
        Functions::expect('has_action')->once()->with(TemplateLoader::class.'.exit')->andReturn(false);

        $exit = false;
        Actions::expectFired(TemplateLoader::class.'.exit')->once()->whenHappen(function () use (
            &$exit
        ) {
            $exit = true;
        });

        $wpQuery = new \WP_Query();
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';

        $finder = Mockery::mock(FinderInterface::class);
        $finder->shouldReceive('findFirst')->once()->with(['index'], 'index')->andReturn($template);

        $loader = new TemplateLoader($finder);

        ob_start();
        $loader->loadAndExit($wpQuery, true);

        assertSame('index', trim(ob_get_clean()));

        assertTrue($exit);
    }
}
