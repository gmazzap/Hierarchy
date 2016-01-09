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
use GM\Hierarchy\Finder\BaseTemplateFinder;
use GM\Hierarchy\Finder\LocalizedTemplateFinder;
use GM\Hierarchy\Finder\SymfonyFinderAdapter;
use GM\Hierarchy\TemplateLoader;
use GM\Hierarchy\Tests\TestCase;
use Mockery;
use Symfony\Component\Finder\Finder;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
class TemplateLoaderTest extends TestCase
{
    public function testLoadPageCustom()
    {
        $post = Mockery::mock('\WP_Post');
        $post->ID = 1;
        $post->post_name = 'a-page';
        $post->post_type = 'page';

        Functions::expect('get_page_template_slug')->with($post)->andReturn('page-templates/page-custom.php');
        Functions::expect('validate_file')->with('page-templates/page-custom.php')->andReturn(0);
        Functions::when('get_stylesheet_directory')->alias(function () {
            return getenv('HIERARCHY_TESTS_BASEPATH');
        });
        Functions::when('get_template_directory')->alias(function () {
            return getenv('HIERARCHY_TESTS_BASEPATH');
        });

        $wpQuery = new \WP_Query(['is_page' => true], $post, ['pagename' => 'a-page']);

        $loader = new TemplateLoader(new BaseTemplateFinder('files', 'twig'));

        ob_start();
        $loader->load($wpQuery);

        assertSame('page custom', trim(ob_get_clean()));
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

        $loader = new TemplateLoader(new LocalizedTemplateFinder(new SymfonyFinderAdapter($finder)));

        ob_start();
        $loader->load($wpQuery);

        assertSame('foo bar', trim(ob_get_clean()));
    }
}
