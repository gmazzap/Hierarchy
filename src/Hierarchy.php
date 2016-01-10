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

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class Hierarchy
{
    /**
     * @var array
     */
    private static $branches = [
        Branch\Branch404::class,
        Branch\BranchSearch::class,
        Branch\BranchFrontPage::class,
        Branch\BranchHome::class,
        Branch\BranchPostTypeArchive::class,
        Branch\BranchTaxonomy::class,
        Branch\BranchAttachment::class,
        Branch\BranchSingle::class,
        Branch\BranchPage::class,
        Branch\BranchCategory::class,
        Branch\BranchTag::class,
        Branch\BranchAuthor::class,
        Branch\BranchDate::class,
        Branch\BranchArchive::class,
        Branch\BranchComments::class,
        Branch\BranchPaged::class,
    ];

    /**
     * @var \WP_Query
     */
    private $query;

    /**
     * @var bool
     */
    private $parsed = false;

    /**
     * @var array
     */
    private $hierarchy = [];

    /**
     * @var array
     */
    private $hierarchyFlat = [];

    /**
     * @param \WP_Query $query
     */
    public function __construct(\WP_Query $query = null)
    {
        $this->query = $query;
    }

    /**
     * Get hierarchy.
     *
     * @param  bool  $flat
     * @return array
     */
    public function get($flat = false)
    {
        $this->parsed or $this->parse(self::$branches);

        return $flat ? $this->hierarchyFlat : $this->hierarchy;
    }

    /**
     * Get flatten hierarchy.
     *
     * @return array
     */
    public function getHierarchyFlat()
    {
        return $this->get(true);
    }

    /**
     * Parse all branches.
     *
     * @param string[] $branches
     */
    private function parse(array $branches)
    {
        /** @var \WP_Query $query */
        $query = $this->query ?: $GLOBALS['wp_query'];
        if (! $query instanceof \WP_Query) {
            return;
        }

        $hierarchy = [];
        $flat = [];

        foreach ($branches as $class) {
            /** @var \GM\Hierarchy\Branch\BranchInterface $branch */
            $branch = new $class();
            $name = $branch->name();
            if ($branch->is($query) && ! isset($hierarchy[$name])) {
                $leaves = $branch->leaves($query);
                $hierarchy[$name] = $leaves;
                $flat = array_merge($flat, $leaves);
            }
        }

        $flat[] = 'index';
        $hierarchy['index'] = ['index'];
        $this->hierarchyFlat = array_values(array_unique($flat));
        $this->hierarchy = $hierarchy;
        $this->parsed = $this->query instanceof \WP_Query || did_action('template_redirect') > 0;
    }
}
