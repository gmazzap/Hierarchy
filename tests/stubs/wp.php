<?php

/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class WP_Error
{
}

class WP_Query
{
    public $true;
    public $object;

    public function __construct(array $true = [], $object = null, array $vars = [])
    {
        $this->true = $true;
        $this->object = $object ?: new \stdClass();
        $this->vars = $vars;
    }

    public function get_queried_object()
    {
        return $this->object;
    }

    public function get($var)
    {
        return isset($this->vars[$var]) ? $this->vars[$var] : '';
    }

    public function __call($name, $arguments)
    {
        if (! array_key_exists($name, $this->true)) {
            return false;
        }

        $want = $this->true[$name];

        return empty($arguments) || $want === true || $want === $arguments;
    }
}
