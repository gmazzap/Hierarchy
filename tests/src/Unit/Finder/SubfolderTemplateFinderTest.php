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

use GM\Hierarchy\Finder\SubfolderTemplateFinder;
use GM\Hierarchy\Tests\TestCase;
use Brain\Monkey\Functions;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class SubfolderTemplateFinderTest extends TestCase
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
        $finder = new SubfolderTemplateFinder('files');

        assertSame('', $finder->find('foo', 'foo'));
    }

    public function testFind()
    {
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';
        $finder = new SubfolderTemplateFinder('files');

        assertSame($template, $finder->find('index', 'index'));
    }

    public function testFindFirst()
    {
        $finder = new SubfolderTemplateFinder('files');
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/another.php';

        assertSame($template, $finder->findFirst(['page-foo', 'another', 'index'], 'page'));
    }
}
