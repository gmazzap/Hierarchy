<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\Hierarchy\Tests\Unit\Finder;

use Brain\Monkey\Functions;
use GM\Hierarchy\Finder\BaseTemplateFinder;
use GM\Hierarchy\Tests\TestCase;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class BaseTemplateFinderTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        Functions::when('get_stylesheet_directory')->alias(function () {
            return getenv('HIERARCHY_TESTS_BASEPATH');
        });
        Functions::when('get_template_directory')->alias(function () {
            return getenv('HIERARCHY_TESTS_BASEPATH');
        });
    }

    public function testFindNothing()
    {
        $finder = new BaseTemplateFinder('files');
        assertSame('', $finder->find('foo', 'foo'));
    }

    public function testFind()
    {
        $finder = new BaseTemplateFinder('files');
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';

        assertSame($template, $finder->find('index', 'index'));
    }

    public function testFindFirst()
    {
        $finder = new BaseTemplateFinder('files');
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/another.php';

        assertSame($template, $finder->findFirst(['page-foo', 'another', 'index'], 'page'));
    }

}