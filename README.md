Hierarchy
=========

Hierarchy is a tiny, simple, no-dependency, class that "embodies" WordPress template hierarchy.

## WTF?

Let me (try to) explain with an example.

Visiting the "uncategorized" category page in a WP site: `http://example.com/category/uncategorized/`

using **`Hierarchy::get()`** method is possible to:

``` php
add_action( 'template_redirect', function() {

  $hierarchy = new GM\Hierarchy;
  print_r( $hierarchy->get() );
} );
```

assuming "uncategorized" term ID is 1, output will be:

    Array
    (
        [category] => Array
            (
                [0] => category-uncategorized
                [1] => category-1
                [2] => category
            )

        [archive] => Array
            (
                [0] => archive
            )

        [index] => Array
            (
                [0] => index
            )
    )

adding a search query var: `http://example.com/category/uncategorized/?s=foo`, output will be:

    Array
    (
        [search] => Array
            (
                [0] => search
            )

        [category] => Array
            (
                [0] => category-uncategorized
                [1] => category-1
                [2] => category
            )

        [archive] => Array
            (
                [0] => archive
            )

        [index] => Array
            (
                [0] => index
            )
    )

So we have an **array representation of WordPress template hierarchy**, not *all* the template hierarchy,
but just the branches that make sense for current query.

Note that array items are **not** file names, are **template hierarchy items**.

Think at the array returned as a code representation of the [template hierarchy graph](http://wphierarchy.com/):
Hierarchy  returns an item for each box of the chart.

Array keys are the branches in Template hierarchy graph that makes sense for current query.

Add `"is_"` to all them (except "index") and you obtain all the [conditional tags](http://codex.wordpress.org/Conditional_Tags)
that return true for current query.

E.g. in the example above `is_search()`, `is_category()` and `is_archive()` are all true for current query.

It worth noting that:

- Hierarchy does **not** interfere with WordPress and not affect (nor is affected) by any WordPress hook
- Is possible to use Hierarchy after (or at least during) `"template_redirect"`: before that hook there
is no current query to check.

Note that at `"template_redirect"` time, WordPress hasn't yet discovered
the template to load, it will be discovered *a few* `file_exists()` later...


## Flattened Hierarchy

Even if the two-dimensional array returned by `Hierarchy::get()` is rich of informations, looping
through it it's harder than looping a simple "flat" array.

Sure using `RecursiveIteratorIterator` and `RecursiveArrayIterator` is possible to loop the array
just like it was flat, but there is something simpler: to use **`Hierarchy::getFlat()`**.

Example:

``` php
add_action( 'template_redirect', function() {

  $hierarchy = new GM\Hierarchy;
  print_r( $hierarchy->getFlat() );
} );
```

Assuming same query as above, output will be:

    Array
    (
        [0] => search
        [1] => category-uncategorized
        [2] => category-1
        [3] => category
        [4] => archive
        [5] => index
    )

## Find templates

The third and last public method of `Hierarchy` class is **`findTemplateUsing()`**: it's an utility method
that allows to find first matching template in the hierarchy array, based on a condition that is passed
to method as PHP callable.

An example:

``` php
add_action( 'template_redirect', function() {

    $hierarchy = new GM\Hierarchy;

    $callback = function( $template, $query_type ) {
      $cached = WP_CONTENT_DIR . '/cache/' . $template . '.html';
      return file_exists( $cached ) ? $cached : FALSE;
    };

    if ( ( $cached = $hierarchy->findTemplateUsing( $callback ) ) ) {
        readfile( $cached );
        exit();
    }
}, 999 );
```

The callback passed to `findTemplateUsing()` receives as first argument the template item,
e. g. `'category-uncategorized'`, `'category-1'`, `'category'` and so on...
as second argument the related query type, i.e. a "parent" key in the hierarchy array,
e. g. `search`, `category`, `archive` and so on.

The callback has to return a non-empty value to stop the function from being called for every item
in the hierarchy array.

If template is found (callback returns something not empty) `Hierarchy::findTemplateUsing()` returns
whatever is returned by callback, otherwise return `false`.



## What Can I do with this class?

Anything? :) Jokes aside, template hierarchy is a key concept in WordPress, a code-readable
representation of it may be used for a lot of applications, sure more than I can imagine now.

Just as example, let's see how to


### Build an indipendent template hierarchy for template partials

See this code:

``` php
function get_hierarchy_header() {

    static $hierarchy = NULL;
    if ( is_null($hierarchy) ) {
        $hierarchy = new GM\Hierarchy;
    }

    $templates = array_map( function( $template ) {
        return "header-{$template}.php";
    }, $hierarchy->getFlat() );

    if ( ! locate_template( $templates ) ) { // fully child-theme friendly!
        get_header(); // fallback
    }
}
```

Using a simple function like that as a replacement for `get_header()` we can automatically load
different header files for different query types, following standard WordPress template scheme,
e.g. `header-single-product.php`, `header-archive.php`... and so on; no matter if "main" WordPress
template exists or not: e.g. `date.php` may not exist, but if `header-date.php` exists it will be
loaded.

Same can be applied to sidebars, footers or any "partial".

Imagine what a `get_hierarchy_partial( $prefix, $suffix )` function can do...


## Just a few examples...

Here I have shown just a couple of trivial and rough examples, but possibilities this simple class
provides are truly many.

===============

# Installation

Hierarchy is a [Composer](https://getcomposer.org/) package and can be added to your project
dependencies via:

``` bash
composer require gmazzap/hierarchy:dev-master --no-update
composer update --no-dev
```

# License

Hierarchy is released under MIT.
