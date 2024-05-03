<?php

namespace ContentTypes;

abstract class CustomPost {
    /**
    * @var String $name Post type name
    */
    protected $name = '';

    /**
    * @var String $singular Nice Singular name
    */
    protected $singular = '';

    /**
    * @var String $plural Nice plural name
    */
    protected $plural = '';

    /**
    * @var String $icon Dash icon
    */
    protected $icon = '';

    /**
    * @var String $taxonomy Taxonomy type name
    */
    protected $taxonomy = '';

    /**
    *   @var Array $args Query arguments
    */
    public $args = '';

    /**
    *   @var Array $args Query arguments
    */
    public $labels = '';

    /**
    *   @method __construct Build the args and labels for registering the post type
    */

    public function __construct() {

        add_theme_support('post-thumbnails');

        $this->labels = array(
            'name'               => _x($this->plural, 'post type general name', LJS_TEXT_DOMAIN),
            'singular_name'      => _x($this->singular, 'post type singular name', LJS_TEXT_DOMAIN),
            'menu_name'          => _x($this->plural, 'admin menu', LJS_TEXT_DOMAIN),
            'name_admin_bar'     => _x($this->singular, 'add new on admin bar', LJS_TEXT_DOMAIN),
            'add_new'            => _x('Add New', 'post', LJS_TEXT_DOMAIN),
            'add_new_item'       => __("Add New {$this->singular}", LJS_TEXT_DOMAIN),
            'new_item'           => __("New {$this->singular}", LJS_TEXT_DOMAIN),
            'edit_item'          => __("Edit {$this->singular}", LJS_TEXT_DOMAIN),
            'view_item'          => __("View {$this->singular}", LJS_TEXT_DOMAIN),
            'all_items'          => __("All {$this->plural}", LJS_TEXT_DOMAIN),
            'search_items'       => __("Search {$this->plural}", LJS_TEXT_DOMAIN),
            'parent_item_colon'  => __("Parent {$this->plural}:", LJS_TEXT_DOMAIN),
            'not_found'          => __("No {$this->plural} found.", LJS_TEXT_DOMAIN),
            'not_found_in_trash' => __("No {$this->plural} found in Trash.", LJS_TEXT_DOMAIN)
        );

        $this->args = array(
            'labels'             => $this->labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => $this->name ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'thumbnail' ),
            'menu_icon'          => $this->icon,
        );
    }

    public function register()
    {
        if(!empty($this->taxonomy)){
            $this->register_taxonomy();
        }
        $this->register_post_type();
    }

    /**
    *   @method register_posttype
    */
    public function register_post_type()
    {
        register_post_type( $this->name , $this->args );
    }

    /**
     *  Register Case Study category Taxonomy
     *
     * @var String $taxonomy (required) The name of the taxonomy.
     * Name should only contain lowercase letters and the underscore character,
     * and not be more than 32 characters long (database structure restriction).
     * Default: None
     *
     * @var Array/String $object_type (required) Name of the object type for
     * the taxonomy object. Object-types can be built-in Post Type or any
     * Custom Post Type that may be registered.
     * Default: None
     *
     * @var Array/String $args (optional) An array of Arguments. Default: None
     */
    public function register_taxonomy () {
        
        foreach( $this->taxonomy as $taxonomy ) {
            register_taxonomy($taxonomy['name'], $this->name, $taxonomy['args']);
        } 
    }
}
