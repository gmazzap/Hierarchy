<?php namespace GM\Tests;

use GM\Hierarchy;

class HierarchyTest extends TestCase
{
    public function testGetDoNothingIfNotTemplateRedirect()
    {
        \WP_Mock::wpFunction('did_action', [
            'args'   => [ 'template_redirect' ],
            'return' => FALSE
        ]);
        $h = new Hierarchy();
        assertSame([ ], $h->get());
    }

    public function testParseHierarchyRunOnce()
    {
        $h = \Mockery::mock('GM\Hierarchy')->makePartial();
        $h->shouldReceive('parsed')->andReturn(true);
        assertSame([ ], $h->get());
    }

    public function testParseHierarchy()
    {
        \WP_Mock::wpFunction('did_action', [
            'args'   => [ 'template_redirect' ],
            'return' => TRUE
        ]);
        \WP_Mock::wpFunction('get_queried_object', [
            'return' => (object) [ 'taxonomy' => 'prodcat', 'slug' => 'tech', 'term_id' => '46' ]
        ]);
        \WP_Mock::wpFunction('get_query_var', [
            'args'   => [ 'post_type' ],
            'return' => 'product'
        ]);
        \WP_Mock::wpFunction('post_type_exists', [
            'args'   => [ 'product' ],
            'return' => TRUE
        ]);
        $h = new Hierarchy();
        foreach (array_keys($h->getBranches()) as $branch) {
            if (in_array($branch, [ 'search', 'tax', 'archive', 'paged' ], true)) {
                \WP_Mock::wpFunction("is_{$branch}", [ 'return' => TRUE ]);
            } else {
                \WP_Mock::wpFunction("is_{$branch}", [ 'return' => FALSE ]);
            }
        }
        $expected = [
            'search'  => [ 'search' ],
            'tax'     => [ 'taxonomy-prodcat-tech', 'taxonomy-prodcat', 'taxonomy' ],
            'archive' => [ 'archive-product', 'archive' ],
            'paged'   => [ 'paged' ],
            'index'   => [ 'index' ],
        ];
        $expected_merged = [
            'search',
            'taxonomy-prodcat-tech',
            'taxonomy-prodcat',
            'taxonomy',
            'archive-product',
            'archive',
            'paged',
            'index',
        ];
        assertSame($expected, $h->get());
        assertSame($expected_merged, $h->getFlat());
    }

    public function testFindTemplateUsing()
    {
        $hierarchy = [
            'search'  => [ 'search' ],
            'tax'     => [ 'taxonomy-prodcat-tech', 'taxonomy-prodcat', 'taxonomy' ],
            'archive' => [ 'archive-product', 'archive' ],
            'paged'   => [ 'paged' ],
            'index'   => [ 'index' ],
        ];
        $h = \Mockery::mock('GM\Hierarchy')->makePartial();
        $h->shouldReceive('get')->andReturn($hierarchy);
        $templates = [ 'foo.php', 'bar.php', 'taxonomy-prodcat.php', 'baz.php' ];
        $callback = function ($template, $type) use ($templates) {
            $file = "{$template}.php";

            return in_array($file, $templates, true) ? [ $file, $type ] : false;
        };
        $nocallback = function ($template, $type) use ($templates) {
            $file = "{$template}.html";

            return in_array($file, $templates, true) ? [ $file, $type ] : false;
        };
        assertSame([ 'taxonomy-prodcat.php', 'tax' ], $h->findTemplateUsing($callback));
        assertFalse($h->findTemplateUsing($nocallback));
    }
}
