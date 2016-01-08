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
 * Search templates looking for "localized" folders.
 *
 * Assuming
 *  - `$subfolder` is "templates"
 *  - `$extension` is "php"
 *  - current locale is "it_IT"
 *  - template to search is "page"
 *  - there is a child theme active
 *
 * It returns the first found among:
 *
 * 1. /path/to/child/theme/templates/it_IT/page.php
 * 2. /path/to/parent/theme/templates/it_IT/page.php
 * 3. /path/to/child/theme/templates/page.php
 * 4. /path/to/parent/theme/templates/page.php
 *
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package Hierarchy
 */
final class LocalizedTemplateFinder implements FinderInterface
{

    use FindFirstTemplateTrait;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var \GM\Hierarchy\Finder\BaseTemplateFinder
     */
    private $finder;

    /**
     * @param \GM\Hierarchy\Finder\FinderInterface $finder
     */
    public function __construct(FinderInterface $finder = null)
    {
        $this->locale = get_locale();
        $this->finder = $finder ? : new BaseTemplateFinder();
    }

    /**
     * @inheritdoc
     */
    public function find($template, $type)
    {
        $templates = [$this->locale . '/'. $template, $template];

        return $this->finder->findFirst($templates, $type);
    }
}
