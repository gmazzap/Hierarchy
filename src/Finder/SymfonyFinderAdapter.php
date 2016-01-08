<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\Hierarchy\Finder;

use Symfony\Component\Finder\Finder;

/**
 * A Symfony Finder adapter. Hierarchy does not ship with Symfony Finder (only on development)
 * so it have to be installed separately.
 *
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
class SymfonyFinderAdapter implements FinderInterface
{
    /**
     * @var \Symfony\Component\Finder\Finder
     */
    private $finder;

    /**
     * @param \Symfony\Component\Finder\Finder $finder
     */
    public function __construct(Finder $finder = null)
    {
        if (is_null($finder)) {
            $stylesheet = trailingslashit(get_stylesheet_directory());
            $template = trailingslashit(get_template_directory());
            $folders = [$stylesheet()];
            ($stylesheet !== $template) and $folders[] = $template;
            $finder = (new Finder())->in($folders);
        }

        $this->finder = $finder->ignoreDotFiles(true)->ignoreUnreadableDirs(true);
    }

    /**
     * @param \Symfony\Component\Finder\Finder $finder
     * @return \GM\Hierarchy\Finder\SymfonyFinderAdapter
     */
    public function withFinder(Finder $finder)
    {
        $clone = clone $this;
        $clone->finder = $finder;

        return $clone;
    }


    /**
     * @inheritdoc
     */
    public function find($template, $type)
    {
        /** @var \Iterator $iterator */
        $iterator = $this->finder->files()->name("#{$template}(\.[\w]{1,})?$#");
        if ($iterator->valid()) {
            $iterator->rewind();

            return $iterator->current();
        }

        return '';
    }


    /**
     * @inheritdoc
     */
    public function findFirst(array $templates, $type)
    {
        $names = '('.implode('|', $templates).')';
        /** @var \Iterator $iterator */
        $iterator = $this->finder->files()->name("#{$names}(\.[\w]{1,})?$#");
        if ($iterator->valid()) {
            $iterator->rewind();

            return $iterator->current();
        }

        return '';
    }
}
