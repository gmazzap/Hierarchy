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

use GM\Hierarchy\Finder\FoldersTemplateFinder;
use GM\Hierarchy\Tests\TestCase;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class FoldersTemplateFinderTest extends TestCase
{
    public function testFindNothing()
    {
        $folders = [getenv('HIERARCHY_TESTS_BASEPATH').'/files'];
        $finder = new FoldersTemplateFinder($folders);

        assertSame('', $finder->find('foo', 'foo'));
    }

    public function testFind()
    {
        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/index.php';

        $folders = [getenv('HIERARCHY_TESTS_BASEPATH').'/files'];
        $finder = new FoldersTemplateFinder($folders);

        assertSame($template, $finder->find('index', 'index'));
    }

    public function testFindFirst()
    {
        $folders = [getenv('HIERARCHY_TESTS_BASEPATH').'/files'];
        $finder = new FoldersTemplateFinder($folders);

        $template = getenv('HIERARCHY_TESTS_BASEPATH').'/files/another.php';

        assertSame($template, $finder->findFirst(['page-foo', 'another', 'index'], 'page'));
    }
}
