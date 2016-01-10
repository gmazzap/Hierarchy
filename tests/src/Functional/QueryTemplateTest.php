<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\Hierarchy\Tests\Functional;

use Brain\Monkey\Functions;
use GM\Hierarchy\Finder\FoldersTemplateFinder;
use GM\Hierarchy\Finder\LocalizedTemplateFinder;
use GM\Hierarchy\Finder\SymfonyTemplateFinderAdapter;
use GM\Hierarchy\QueryTemplate;
use GM\Hierarchy\Tests\TestCase;
use Mockery;
use Symfony\Component\Finder\Finder;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
class QueryTemplateTest extends TestCase
{
    public function testLoadPageCustom()
    {
        $post = Mockery::mock('\WP_Post');
        $post->ID = 1;
        $post->post_name = 'a-page';
        $post->post_type = 'page';

        Functions::expect('get_page_template_slug')->with($post)->andReturn('page-templates/page-custom.php');
        Functions::expect('validate_file')->with('page-templates/page-custom.php')->andReturn(0);

        $wpQuery = new \WP_Query(['is_page' => true], $post, ['pagename' => 'a-page']);

        $folders = [getenv('HIERARCHY_TESTS_BASEPATH').'/files'];
        $loader = new QueryTemplate(new FoldersTemplateFinder($folders, 'twig'));

        assertSame('page custom', $loader->loadTemplate($wpQuery));
    }

    public function testLocalizedTaxonomy()
    {
        Functions::when('get_stylesheet_directory')->alias(function () {
            return getenv('HIERARCHY_TESTS_BASEPATH');
        });

        Functions::when('get_locale')->alias(function () {
            return 'it_IT';
        });

        $wpQuery = new \WP_Query([
            'is_tax'     => true,
            'is_archive' => true,
        ], (object) ['slug' => 'bar', 'taxonomy' => 'foo']);

        $finder = new Finder();
        $finder->in([get_stylesheet_directory().'/files'])
               ->ignoreDotFiles(true)
               ->ignoreUnreadableDirs(true)
               ->followLinks();

        $loader = new QueryTemplate(new LocalizedTemplateFinder(new SymfonyTemplateFinderAdapter($finder)));

        assertSame('foo bar', $loader->loadTemplate($wpQuery));
    }

    public function testFallbackToArchive()
    {
        Functions::when('get_stylesheet_directory')->alias(function () {
            return getenv('HIERARCHY_TESTS_BASEPATH').'/files/it_IT';
        });

        $wpQuery = new \WP_Query([
            'is_tax'     => true,
            'is_archive' => true,
        ], (object) ['slug' => 'bar', 'taxonomy' => 'foo']);

        $loader = new QueryTemplate();

        assertSame('archive', $loader->loadTemplate($wpQuery));
    }

    public function testFallbackToIndex()
    {
        Functions::when('get_stylesheet_directory')->alias(function () {
            return getenv('HIERARCHY_TESTS_BASEPATH').'/files';
        });

        $wpTaxQuery = new \WP_Query([
            'is_tax'     => true,
            'is_archive' => true,
        ], (object) ['slug' => 'bar', 'taxonomy' => 'foo']);

        $wpSearchQuery = new \WP_Query([
            'is_search' => true,
        ]);

        $loader = new QueryTemplate();

        assertSame('index', $loader->loadTemplate($wpTaxQuery));
        assertSame('index', $loader->loadTemplate($wpSearchQuery));
    }
}
