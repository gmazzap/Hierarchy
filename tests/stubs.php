<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    class WP_Error
    {

    }

}

namespace GM\Hierarchy\Tests\Stubs {

    use GM\Hierarchy\Branch\BranchInterface;
    use WP_Query;

    class BranchStubFoo implements BranchInterface
    {

        public function name()
        {
            return 'foo';
        }

        public function is(WP_Query $query)
        {
            return true;
        }

        /**
         * @return array
         */
        public function leaves()
        {
            return ['foo', 'bar'];
        }
    }

    class BranchStubBar implements BranchInterface
    {

        public function name()
        {
            return 'bar';
        }

        public function is(WP_Query $query)
        {
            return true;
        }

        /**
         * @return array
         */
        public function leaves()
        {
            return ['baz', 'bar'];
        }
    }

    class BranchStubBar2 implements BranchInterface
    {

        public function name()
        {
            return 'bar';
        }

        public function is(WP_Query $query)
        {
            return true;
        }

        /**
         * @return array
         */
        public function leaves()
        {
            return ['a', 'b', 'c'];
        }
    }

    class BranchStubBaz implements BranchInterface
    {

        public function name()
        {
            return 'baz';
        }

        public function is(WP_Query $query)
        {
            return false;
        }

        /**
         * @return array
         */
        public function leaves()
        {
            return ['1', '2', 3];
        }
    }
}

