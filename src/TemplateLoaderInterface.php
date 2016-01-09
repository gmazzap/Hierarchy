<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\Hierarchy;

use GM\Hierarchy\Finder\MultiFinderInterface;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
interface TemplateLoaderInterface
{

    /**
     * Find a template for the given WP_Query.
     * If no WP_Query provided, global \WP_Query is used.
     * By default, found template passes through "{$type}_template" filter.
     *
     * @param \WP_Query $query
     * @param bool      $filters Pass the found template through filter?
     * @return string
     */
    public function find(\WP_Query $query = null, $filters = true);

    /**
     * Find a template for the given query and load it.
     * If no WP_Query provided, global \WP_Query is used.
     * By default, found template passes through "{$type}_template" and "template_include" filters.
     * Optionally exit the request after having loaded the template.
     *
     * @param \WP_Query|null $query
     * @param bool           $filters Pass the found template through filters?
     * @param bool           $exit    Exit the request after having included the template?
     */
    public function load(\WP_Query $query = null, $filters = true, $exit = false);

}
