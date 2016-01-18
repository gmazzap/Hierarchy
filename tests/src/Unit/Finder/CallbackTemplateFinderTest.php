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

use GM\Hierarchy\Finder\CallbackTemplateFinder;
use GM\Hierarchy\Tests\TestCase;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class CallbackTemplateFinderTest extends TestCase
{
    public function testFindNothing()
    {
        $finder = new CallbackTemplateFinder(function () {
            return '';
        });
        assertSame('', $finder->find('index', 'index'));
    }

    public function testFind()
    {
        $path = getenv('HIERARCHY_TESTS_BASEPATH').'/files/';
        $finder = new CallbackTemplateFinder(function ($name, $type) use ($path) {
            assertSame('index', $type);

            return "{$path}{$name}.php";
        });

        assertSame("{$path}index.php", $finder->find('index', 'index'));
    }

    public function testFindFirst()
    {
        $path = getenv('HIERARCHY_TESTS_BASEPATH').'/files/';
        $finder = new CallbackTemplateFinder(function (array $names, $type) use ($path) {
            $name = array_pop($names);

            assertSame('page', $type);

            return "{$path}{$name}.php";
        });

        assertSame("{$path}another.php", $finder->find(['page', 'another'], 'page'));
    }
}
