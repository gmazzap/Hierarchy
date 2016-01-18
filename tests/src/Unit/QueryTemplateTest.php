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

use Andrew\Proxy;
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
class QueryTemplateTest extends TestCase
{
    public function testFindEmptyWhenNoLeaves()
    {
        $finder = Mockery::mock(TemplateFinderInterface::class);
        $loader = new QueryTemplate($finder);
        $found = $loader->findTemplate();

        assertSame('', $found);
    }

    public function testFindNoFilters()
    {
        $wpQuery = new \WP_Query();
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';
        $finder = Mockery::mock(TemplateFinderInterface::class);
        $finder->shouldReceive('findFirst')->once()->with(['index'], 'index')->andReturn($template);

        $loader = new QueryTemplate($finder);

        assertSame($template, $loader->findTemplate($wpQuery, false));
    }

    public function testFindFilters()
    {
        Filters::expectApplied('index_template')->once()->andReturn('foo.php');

        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';
        $wpQuery = new \WP_Query();
        $finder = Mockery::mock(TemplateFinderInterface::class);
        $finder->shouldReceive('findFirst')->once()->with(['index'], 'index')->andReturn($template);

        $loader = new QueryTemplate($finder);

        assertSame('foo.php', $loader->findTemplate($wpQuery, true));
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

    public function testApplyFilters()
    {
        define('DIE', 1);

        $wpQuery = Mockery::mock('WP_Query');
        $wpQuery->shouldReceive('is_main_query')->withNoArgs()->andReturn(true);

        global $wp_query, $wp_the_query;
        $wp_query = $wp_the_query = $wpQuery;
        $customQuery = new \WP_Query();

        // during filter, globals `$wp_query` and `$wp_the_query` are equal to custom query
        Filters::expectApplied('test_filter')
            ->once()
            ->with('foo')
            ->andReturnUsing(
                function() use($customQuery) {
                    assertSame($GLOBALS['wp_query'], $customQuery);
                    assertSame($GLOBALS['wp_the_query'], $customQuery);

                    return 'bar!';
                }
            );

        $queryTemplate = new Proxy(new QueryTemplate());
        $applied = $queryTemplate->applyFilter('test_filter', 'foo', $customQuery);

        // after filter, globals `$wp_query` and `$wp_the_query` are restored

        assertSame($GLOBALS['wp_query'], $wpQuery);
        assertSame($GLOBALS['wp_the_query'], $wpQuery);
        assertSame('bar!', $applied);

        unset($wp_query, $wp_the_query);
    }
}
