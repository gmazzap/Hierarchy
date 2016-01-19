<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\Hierarchy\Tests\Stubs;

use GM\Hierarchy\Branch\BranchInterface;

class BranchStubFoo implements BranchInterface
{
    /**
     * @inheritdoc
     */
    public function name()
    {
        return 'foo';
    }

    /**
     * @inheritdoc
     */
    public function is(\WP_Query $query)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function leaves(\WP_Query $query)
    {
        return ['foo', 'bar'];
    }
}

class BranchStubBar implements BranchInterface
{
    /**
     * @inheritdoc
     */
    public function name()
    {
        return 'bar';
    }

    /**
     * @inheritdoc
     */
    public function is(\WP_Query $query)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function leaves(\WP_Query $query)
    {
        return ['baz', 'bar'];
    }
}

class BranchStubBar2 implements BranchInterface
{
    /**
     * @inheritdoc
     */
    public function name()
    {
        return 'bar';
    }

    /**
     * @inheritdoc
     */
    public function is(\WP_Query $query)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function leaves(\WP_Query $query)
    {
        return ['a', 'b', 'c'];
    }
}

class BranchStubBaz implements BranchInterface
{
    /**
     * @inheritdoc
     */
    public function name()
    {
        return 'baz';
    }

    /**
     * @inheritdoc
     */
    public function is(\WP_Query $query)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function leaves(\WP_Query $query)
    {
        return ['1', '2', 3];
    }
}
