<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\Hierarchy\Branch;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class BranchSingle implements BranchInterface
{
    /**
     * @inheritdoc
     */
    public function name()
    {
        return 'single';
    }

    /**
     * @inheritdoc
     */
    public function is(\WP_Query $query)
    {
        return $query->is_single();
    }

    /**
     * @inheritdoc
     */
    public function leaves()
    {
        /** @var \WP_Post $post */
        $post = get_queried_object();
        $post instanceof \WP_Post or $post = new \WP_Post((object)['ID' => 0]);

        $leaves = empty($post->post_type) ? [] : ["single-{$post->post_type}"];
        $leaves[] = 'single';
        $leaves[] = 'singular';

        return $leaves;
    }
}
