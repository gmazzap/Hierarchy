<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\Hierarchy\Loader;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class FileIncludeLoader implements TemplateLoaderInterface
{
    /**
     * @param string $templatePath
     */
    public function load($templatePath)
    {
        $production = ! defined('WP_DEBUG') || ! WP_DEBUG;
        if (! $production) {
            /** @noinspection PhpIncludeInspection */
            include $templatePath;
        } elseif (file_exists($templatePath)) {
            /** @noinspection PhpIncludeInspection */
            include $templatePath;
        }
    }
}
