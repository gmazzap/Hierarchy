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
     * @var string
     */
    private $type = '';

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
        $type = $query->get('post_type');
        if (is_array($type)) {
            $type = reset($post_type);
        }

        $object = get_post_type_object($type);
        $is = is_object($object) && ! empty($object->has_archive);
        $this->type = $is ? $type : '';

        return $is;
    }

    /**
     * @inheritdoc
     */
    public function leaves(\WP_Query $query)
    {
        return $this->type ? ["archive-{$this->type}", 'archive'] : ['archive'];
    }
}
