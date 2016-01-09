<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\Hierarchy\Finder;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 *
 * @method string find(string $template, string $type)
 */
trait FindFirstTemplateTrait
{
    /**
     * @param  array       $templates
     * @param  string      $type
     * @return string|bool
     * @see \GM\Hierarchy\Finder\FinderInterface::findFirst()
     */
    public function findFirst(array $templates, $type)
    {
        $found = '';
        while (! empty($templates) && $found === '') {
            $found = $this->find(array_shift($templates), $type) ?: '';
        }

        return $found;
    }
}
