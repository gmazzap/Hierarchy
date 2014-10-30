<?php namespace GM;

class Hierarchy {

    private static $branches = [
        '404'               => [ [ '404' ], FALSE ],
        'search'            => [ [ 'search' ], FALSE ],
        'front_page'        => [ [ 'front-page' ], FALSE ],
        'home'              => [ [ 'home' ], FALSE ],
        'post_type_archive' => [ [ 'archive' ], FALSE ],
        'tax'               => [ [ 'taxonomy-{{tax}}-{{slug}}', 'taxonomy-{{tax}}', 'taxonomy' ], TRUE ],
        'attachment'        => [ [ 'attachment' ], FALSE ],
        'single'            => [ [ 'single-{{slug}}', 'single' ], TRUE ],
        'page'              => [ [ 'page-{{slug}}', 'page-{{id}}', 'page' ], TRUE ],
        'category'          => [ [ 'category-{{slug}}', 'category-{{id}}', 'category' ], TRUE ],
        'tag'               => [ [ 'tag-{{slug}}', 'tag-{{id}}', 'tag' ], TRUE ],
        'author'            => [ [ 'author-{{slug}}', 'author-{{id}}', 'author' ], TRUE ],
        'date'              => [ [ 'date' ], FALSE ],
        'archive'           => [ [ 'archive' ], FALSE ],
        'comments_popup'    => [ [ 'comments-popup.php' ], FALSE ],
        'paged'             => [ [ 'paged' ], FALSE ]
    ];
    private $parsed = FALSE;
    private $hierarchy = [ ];
    private $hierarchy_merged = [ ];

    /**
     * @return boolean
     */
    public function parsed() {
        return $this->parsed;
    }

    /**
     * @return array
     */
    public function get( $merged = FALSE ) {
        if ( did_action( 'template_redirect' ) && ! $this->parsed() ) {
            $this->parseHierarchy();
        }
        return $merged ? $this->hierarchy_merged : $this->hierarchy;
    }

    /**
     * @return array
     */
    public function getFlat() {
        return $this->get( TRUE );
    }

    /**
     * Find a template using a given callback that receive a single hierarchy item and related
     * query type.
     * Callback have to return a non-falsey value to stop the looping through hierarchy array.
     * If template is not found method return FALSE.
     * If template is found (callback return something not empty) methodd return whatever is returned
     * by callback.
     *
     * @param callable $callable
     * @return mixed
     */
    public function findTemplateUsing( callable $callable ) {
        $hierarchy = $this->get();
        $types = array_keys( $hierarchy );
        $found = FALSE;
        while ( ! empty( $types ) && ! $found ) {
            $type = array_shift( $types );
            while ( ! empty( $hierarchy[ $type ] ) && ! $found ) {
                $found = call_user_func( $callable, array_shift( $hierarchy[ $type ] ), $type );
            }
        }
        return $found;
    }

    /**
     * Gives access to branches array
     *
     * @return array
     */
    public function getBranches() {
        return self::$branches;
    }

    private function parseHierarchy() {
        $merged = [ ];
        foreach ( array_keys( $this->getBranches() ) as $branch ) {
            $merged = $this->parseBranch( $branch, $merged );
        }
        $merged[] = 'index';
        $this->hierarchy[ 'index' ] = [ 'index' ];
        $this->hierarchy_merged = array_values( array_unique( $merged ) );
        $this->parsed = TRUE;
    }

    private function parseBranch( $type, array $merged ) {
        if ( ! call_user_func( "is_{$type}" ) ) {
            return $merged;
        }
        $branches = array_diff( $this->branchVariables( $type, $this->typeBranches( $type ) ), $merged );
        if ( ! empty( $branches ) ) {
            $this->hierarchy[ $type ] = $branches;
            $merged = array_merge( $merged, $branches );
        }
        return $merged;
    }

    private function typeBranches( $branch ) {
        $branches = $this->getBranches();
        $tmpls = $branches[ $branch ][ 0 ];
        $cb = 'get' . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $branch ) ) ) . 'Branches';
        return method_exists( $this, $cb ) ? $this->$cb( $tmpls ) : $tmpls;
    }

    private function branchVariables( $branch, array $hierarchy ) {
        $branches = $this->getBranches();
        if ( ! $branches[ $branch ][ 1 ] ) {
            return $hierarchy;
        }
        $queried = get_queried_object();
        if ( ! is_object( $queried ) || empty( $queried ) ) {
            return $hierarchy;
        }
        if ( in_array( $branch, [ 'tax', 'tag', 'category' ], TRUE ) && isset( $queried->term_id ) ) {
            $vars = [ $queried->slug, $queried->term_id, $queried->taxonomy ];
        } elseif ( $branch === 'author' && isset( $queried->user_nicename ) ) {
            $vars = [ $queried->user_nicename, $queried->ID, NULL ];
        } elseif ( $branch === 'single' && isset( $queried->post_type ) ) {
            $vars = [ $queried->post_type, $queried->ID, NULL ];
        } elseif ( $branch === 'page' && isset( $queried->post_name ) ) {
            $vars = [ $queried->post_name, $queried->ID, NULL ];
        }
        return $this->replaceBranchVars( $hierarchy, $vars );
    }

    private function getPostTypeArchiveBranches( array $tmpls ) {
        $post_type = get_query_var( 'post_type' );
        if ( is_array( $post_type ) ) {
            $post_type = reset( $post_type );
        }
        $obj = get_post_type_object( $post_type );
        if ( ! $obj->has_archive ) {
            return [ ];
        }
        array_unshift( $tmpls, "archive-{$post_type}" );
        return $tmpls;
    }

    private function getArchiveBranches( array $tmpls ) {
        $post_types = array_filter( (array) get_query_var( 'post_type' ) );
        if ( count( $post_types ) == 1 ) {
            $post_type = array_shift( $post_types );
            if ( post_type_exists( $post_type ) ) {
                array_unshift( $tmpls, "archive-{$post_type}" );
            }
        }
        return $tmpls;
    }

    private function getPageBranches( array $tmpls ) {
        $file = filter_var( get_page_template_slug(), FILTER_VALIDATE_URL );
        if ( ! empty( $file ) && validate_file( $file ) === 0 ) {
            $cut = strlen( pathinfo( $file, PATHINFO_EXTENSION ) ) * -1;
            array_unshift( $tmpls, substr( $file, 0, $cut < 0 ? $cut - 1 : strlen( $file )  ) );
        }
        return $tmpls;
    }

    private function getAttachmentBranches() {
        global $posts;
        if ( empty( $posts ) || ! isset( $posts[ 0 ]->post_mime_type ) ) {
            return [ 'attachment' ];
        }
        $type = explode( '/', $posts[ 0 ]->post_mime_type );
        if ( empty( $type ) ) {
            return [ 'attachment' ];
        }
        $tmpls = [ $type[ 0 ] ];
        if ( ! empty( $type[ 1 ] ) ) {
            $tmpls = array_merge( $tmpls, [ $type[ 1 ], "{$type[ 0 ]}_{$type[ 1 ]}" ] );
        }
        $tmpls[] = 'attachment';
        return $tmpls;
    }

    private function replaceBranchVars( array $types, array $vars ) {
        array_walk( $types, function( &$branch, $i, $vars ) {
            if ( preg_match( '#{{slug}}|{{id}}|{{tax}}#', $branch ) === 1 ) {
                $branch = str_replace( [ '{{slug}}', '{{id}}', '{{tax}}' ], $vars, $branch );
            }
        }, $vars );
        return $types;
    }

}