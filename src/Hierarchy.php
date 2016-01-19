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
class Hierarchy
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
     * Get hierarchy.
     *
     * @param  \WP_Query $query
     * @return array
     */
    public function getHierarchy(\WP_Query $query = null)
    {
        return $this->parse($query)->hierarchy;
    }

    /**
     * Get flatten hierarchy.
     *
     * @param  \WP_Query $query
     * @return array
     */
    public function getTemplates(\WP_Query $query = null)
    {
        return $this->parse($query)->templates;
    }

    /**
     * Parse all branches.
     *
     * @param  \WP_Query $query
     * @return \stdClass
     */
    private function parse(\WP_Query $query = null)
    {
        (is_null($query) && isset($GLOBALS['wp_query'])) and $query = $GLOBALS['wp_query'];

        $data = (object) ['hierarchy' => [], 'templates' => [], 'query' => $query];

        if ($query instanceof \WP_Query) {
            $data = array_reduce(self::$branches, [$this, 'parseBranch'], $data);
            $data->templates[] = 'index';
            $data->hierarchy['index'] = ['index'];
        }

        $data->templates = array_values(array_unique($data->templates));

        return $data;
    }

    /**
     * @param  string    $branchClass
     * @param  \stdClass $data
     * @return \stdClass
     */
    private function parseBranch(\stdClass $data, $branchClass)
    {
        /** @var \GM\Hierarchy\Branch\BranchInterface $branch */
        $branch = new $branchClass();
        $name = $branch->name();
        if ($branch->is($data->query) && ! isset($data->hierarchy[$name])) {
            $leaves = $branch->leaves($data->query);
            $data->hierarchy[$name] = $leaves;
            $data->templates = array_merge($data->templates, $leaves);
        }

        return $data;
    }
}
