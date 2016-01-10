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

use ArrayIterator;

/**
 * Very similar the way WordPress core works, however, it allows to search templates arbitrary
 * folders and to use a custom file extension (default to php). By default, stylesheet and template
 * folders and file extension to php, so it acts exactly like core.
 *
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class FoldersTemplateFinder implements TemplateFinderInterface
{
    use FindFirstTemplateTrait;

    /**
     * @var \ArrayIterator
     */
    private $folders;

    /**
     * @var string
     */
    private $extension;

    /**
     * @param array  $folders
     * @param string $extension
     */
    public function __construct(array $folders = [], $extension = 'php')
    {
        if (empty($folders)) {
            $stylesheet = trailingslashit(get_stylesheet_directory());
            $template = trailingslashit(get_template_directory());
            $folders = [$stylesheet];
            ($stylesheet !== $template) and $folders[] = $template;
        }

        $this->folders = new ArrayIterator($folders);
        $this->extension = $extension;
    }

    /**
     * @inheritdoc
     */
    public function find($template, $type)
    {
        $found = '';
        $this->folders->rewind();
        while ($this->folders->valid() && $found === '') {
            $path = $this->folders->current()."/{$template}.{$this->extension}";
            $found = file_exists($path) ? $path : '';
            $this->folders->next();
        }

        return $found;
    }
}
