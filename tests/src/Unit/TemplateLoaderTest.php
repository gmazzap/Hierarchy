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

use Brain\Monkey\WP\Filters;
use GM\Hierarchy\QueryTemplate;
use GM\Hierarchy\Tests\TestCase;
use GM\Hierarchy\Finder\TemplateFinderInterface;
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
        $finder = Mockery::mock(TemplateFinderInterface::class);

        $loader = new QueryTemplate($finder);
        $this->setPrivateVar('found', 'xxx', $loader);

        assertSame('xxx', $loader->find());
    }

    public function testFindEmptyWhenNoLeaves()
    {
        $finder = Mockery::mock(TemplateFinderInterface::class);
        $GLOBALS['wp_query'] = '';
        $loader = new QueryTemplate($finder);
        $found = $loader->find();
        unset($GLOBALS['wp_query']);

        assertSame('', $found);
    }

    public function testFindNoFilters()
    {
        $wpQuery = new \WP_Query();
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';
        $finder = Mockery::mock(TemplateFinderInterface::class);
        $finder->shouldReceive('findFirst')->once()->with(['index'], 'index')->andReturn($template);

        $loader = new QueryTemplate($finder);

        assertSame($template, $loader->find($wpQuery, false));
    }

    public function testFindFilters()
    {
        Filters::expectApplied('index_template')->once()->andReturn('foo.php');

        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';
        $wpQuery = new \WP_Query();
        $finder = Mockery::mock(TemplateFinderInterface::class);
        $finder->shouldReceive('findFirst')->once()->with(['index'], 'index')->andReturn($template);

        $loader = new QueryTemplate($finder);

        assertSame('foo.php', $loader->find($wpQuery, true));
    }

    public function testLoadNoFilters()
    {
        $wpQuery = new \WP_Query();
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';
        $finder = Mockery::mock(TemplateFinderInterface::class);
        $finder->shouldReceive('findFirst')->once()->with(['index'], 'index')->andReturn($template);

        $loader = new QueryTemplate($finder);

        assertSame('index', $loader->loadTemplate($wpQuery, false));
    }

    public function testLoadFilters()
    {
        $wpQuery = new \WP_Query();
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/another.php';

        Filters::expectApplied('template_include')->once()->andReturn($template);

        $finder = Mockery::mock(TemplateFinderInterface::class);
        $finder->shouldReceive('findFirst')->once()->with(['index'], 'index')->andReturn('foo');

        $loader = new QueryTemplate($finder);

        assertSame('another', $loader->loadTemplate($wpQuery, true));
    }
}
