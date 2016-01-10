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
final class BranchPostTypeArchive implements BranchInterface
{
    /**
     * @inheritdoc
     */
    public function name()
    {
        return 'archive';
    }

    /**
     * @inheritdoc
     */
    public function is(\WP_Query $query)
    {
        if (! $query->is_post_type_archive()) {
            return false;
        }

        $object = get_post_type_object($this->postType($query));

        return is_object($object) && ! empty($object->has_archive);
    }

    /**
     * @inheritdoc
     */
    public function leaves(\WP_Query $query)
    {
        $type = $this->postType($query);

        return $type ? ["archive-{$type}", 'archive'] : ['archive'];
    }

    /**
     * @param  \WP_Query    $query
     * @return mixed|string
     */
    private function postType(\WP_Query $query)
    {
        $type = $query->get('post_type');
        if (is_array($type)) {
            $type = reset($post_type);
        }

        return is_string($type) ? $type : '';
    }
}
