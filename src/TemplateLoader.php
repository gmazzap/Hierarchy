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

use GM\Hierarchy\Finder\BaseTemplateFinder;
use GM\Hierarchy\Finder\FinderInterface;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
class TemplateLoader implements TemplateLoaderInterface
{
    /**
     * @var callable
     */
    private $finder;

    /**
     * @var string
     */
    private $found = null;

    /**
     * @param \GM\Hierarchy\Finder\FinderInterface|null $finder
     */
    public function __construct(FinderInterface $finder = null)
    {
        // if no finder provided, let's use the one that simulates core behaviour
        is_null($finder) and $finder = new BaseTemplateFinder();

        $this->finder = $finder;
    }

    /**
     * Find a template for the given WP_Query.
     * If no WP_Query provided, global \WP_Query is used.
     * By default, found template passes through "{$type}_template" filter.
     *
     * @param  \WP_Query $query
     * @param  bool      $filters Pass the found template through filter?
     * @return string
     */
    public function find(\WP_Query $query = null, $filters = true)
    {
        if (is_string($this->found)) {
            return $this->found;
        }

        $leaves = (new Hierarchy($query))->get();

        if (! is_array($leaves) || empty($leaves)) {
            return '';
        }

        $types = array_keys($leaves);
        $found = '';
        while (! empty($types) && ! $found) {
            $type = array_shift($types);
            $found = $this->finder->findFirst($leaves[$type], $type);
            $filters and $found = $this->applyFilter("{$type}_template", $found, $query);
        }

        (is_string($found) && $found) and $this->found = $found;

        return $found;
    }

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
    public function load(\WP_Query $query = null, $filters = true, $exit = false)
    {
        $template = $this->find($query, $filters);
        $filters and $template = $this->applyFilter('template_include', $template, $query);

        /** @noinspection PhpIncludeInspection */
        (is_file($template) && is_readable($template)) and include $template;

        if ($exit) {
            // to make the function testable, the exit() call is wrapped in a action
            has_action(__CLASS__.'.exit') or add_action(__CLASS__.'.exit', function () {
                exit();
            }, 30);

            do_action(__CLASS__.'.exit');
        }
    }

    /**
     * A shortcut for load() where 3rd argument is true.
     *
     * @param \WP_Query $query
     * @param bool      $filters
     * @see load()
     */
    public function loadAndExit(\WP_Query $query = null, $filters = true)
    {
        $this->load($query, $filters, true);
    }

    /**
     * To maximize compatibility, when applying a filters and the WP_Query object we are using is
     * NOT the main query, we temporarily set global $wp_query and $wp_the_query to our custom query
     *
     * @param  string    $filter
     * @param  string    $value
     * @param  \WP_Query $query
     * @return string
     */
    private function applyFilter($filter, $value, \WP_Query $query)
    {
        $backup = [];
        $custom = ! $query->is_main_query();
        global $wp_query, $wp_the_query;
        if ($custom && $wp_query instanceof \WP_Query && $wp_the_query instanceof \WP_Query) {
            $backup = [clone $wp_query, clone $wp_the_query];
            unset($wp_query, $wp_the_query);
            $wp_query = $wp_the_query = $query;
        }

        $result = apply_filters($filter, $value);

        if ($custom && $backup) {
            unset($wp_query, $wp_the_query);
            list($wp_query, $wp_the_query) = $backup;
        }

        return $result;
    }
}
