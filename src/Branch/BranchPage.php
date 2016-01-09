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
final class BranchPage implements BranchInterface
{
    /**
     * @inheritdoc
     */
    public function name()
    {
        return 'page';
    }

    /**
     * @inheritdoc
     */
    public function is(\WP_Query $query)
    {
        return $query->is_page();
    }

    /**
     * @inheritdoc
     */
    public function leaves()
    {
        /** @var \WP_Post $post */
        $post = get_queried_object();

        $post instanceof \WP_Post or $post = new \WP_Post((object) ['ID' => 0]);
        $pagename = get_query_var('pagename');

        if (empty($post->post_name) && empty($pagename)) {
            return ['page', 'singular'];
        }

        $name = $pagename ? $pagename : $post->post_name;

        $leaves = [
            "page-{$name}",
            "page-{$post->ID}",
            'page',
            'singular',
        ];

        $template = $post->post_type === 'page'
            ? filter_var(get_page_template_slug($post), FILTER_SANITIZE_URL)
            : false;

        if (! empty($template) && validate_file($template) === 0) {
            $dir = dirname($template);
            $filename = pathinfo($template, PATHINFO_FILENAME);
            $name = $dir === '.' ? $filename : "{$dir}/{$filename}";
            array_unshift($leaves, $name);
        }

        return $leaves;
    }
}
