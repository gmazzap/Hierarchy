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

use GM\Hierarchy\Finder\FoldersTemplateFinder;
use GM\Hierarchy\Finder\TemplateFinderInterface;
use GM\Hierarchy\Loader\FileRequireLoader;
use GM\Hierarchy\Loader\TemplateLoaderInterface;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
class QueryTemplate implements QueryTemplateInterface
{
    /**
     * @var \GM\Hierarchy\Finder\TemplateFinderInterface
     */
    private $finder;

    /**
     * @var \GM\Hierarchy\Loader\TemplateLoaderInterface
     */
    private $loader;

    /**
     * @param \GM\Hierarchy\Loader\TemplateLoaderInterface|null $loader
     * @return \GM\Hierarchy\QueryTemplate
     */
    public static function instanceWithLoader(TemplateLoaderInterface $loader = null)
    {
        return new static(new FoldersTemplateFinder(), $loader);
    }

    /**
     * @param array                                             $folders
     * @param \GM\Hierarchy\Loader\TemplateLoaderInterface|null $loader
     * @return \GM\Hierarchy\QueryTemplate
     */
    public static function instanceWithFolders(
        array $folders,
        TemplateLoaderInterface $loader = null
    ) {
        return new static(new FoldersTemplateFinder($folders), $loader);
    }

    /**
     * @return bool
     */
    public static function mainQueryTemplateAllowed() {

        return
            (
                filter_input(INPUT_SERVER, 'REQUEST_METHOD') !== 'HEAD'
                || ! apply_filters('exit_on_http_head', true)
            )
            && ! is_robots()
            && ! is_feed()
            && ! is_trackback()
            && ! is_embed();
    }

    /**
     * @param \GM\Hierarchy\Finder\TemplateFinderInterface|null $finder
     * @param \GM\Hierarchy\Loader\TemplateLoaderInterface      $loader
     */
    public function __construct(
        TemplateFinderInterface $finder = null,
        TemplateLoaderInterface $loader = null
    ) {
        // if no finder provided, let's use the one that simulates core behaviour
        $this->finder = $finder ?: new FoldersTemplateFinder();
        $this->loader = $loader ?: new FileRequireLoader();
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
    public function findTemplate(\WP_Query $query = null, $filters = true)
    {
        $leaves = (new Hierarchy())->getHierarchy($query);

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

        return $found;
    }

    /**
     * Find a template for the given query and load it.
     * If no WP_Query provided, global \WP_Query is used.
     * By default, found template passes through "{$type}_template" and "template_include" filters.
     *
     * @param  \WP_Query|null $query
     * @param  bool           $filters Pass the found template through filters?
     * @return string
     */
    public function loadTemplate(\WP_Query $query = null, $filters = true)
    {
        $template = $this->findTemplate($query, $filters);
        $filters and $template = $this->applyFilter('template_include', $template, $query);

        return is_file($template) && is_readable($template)
            ? $this->loader->load($template)
            : '';
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
            $backup = [$wp_query, $wp_the_query];
            unset($GLOBALS['wp_query'], $GLOBALS['wp_the_query']);
            $GLOBALS['wp_query'] = $GLOBALS['wp_the_query'] = $query;
        }

        $result = apply_filters($filter, $value);

        if ($custom && $backup) {
            unset($GLOBALS['wp_query'], $GLOBALS['wp_the_query']);
            list($wpQuery, $wpTheQuery) = $backup;
            $GLOBALS['wp_query'] = $wpQuery;
            $GLOBALS['wp_the_query'] = $wpTheQuery;
        }

        return $result;
    }
}
