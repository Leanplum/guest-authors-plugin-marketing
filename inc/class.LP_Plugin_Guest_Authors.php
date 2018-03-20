<?php

if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Guest authors functionality.
 *
 * Allow multiple authors to be credited for a post.
 */
class LP_Plugin_Guest_Authors {

	private static $instance;
	public $objects_to_taxonomies = array();

	/**
	 * Get instance of class.
	 */
	public static function get_instance() {
		if ( !self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {

		// hook to WP
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_filter( 'the_author_posts_link', array( $this, 'filter_the_author_posts_link' ) );
		add_filter( 'the_author', array( $this, 'filter_the_author' ) );
	}

	/**
	 * Register custom taxonomies.
	 *
	 * @return void
	 */
	public function register_taxonomies() {

		$taxonomies_to_register = array();

		/**
		 *  ## Guest Author
		 */
		$labels = array(
			'name' => _x( 'Guest Authors', 'taxonomy general name', 'lp-guest-authors' ),
			'singular_name' => _x( 'Guest Author', 'taxonomy singular name', 'lp-guest-authors' ),
			'search_items' => __( 'Search Authors', 'lp-guest-authors' ),
			'all_items' => __( 'All Authors', 'lp-guest-authors' ),
			'parent_item' => __( 'Parent Author', 'lp-guest-authors' ),
			'parent_item_colon' => __( 'Parent Author:', 'lp-guest-authors' ),
			'edit_item' => __( 'Edit Author', 'lp-guest-authors' ),
			'update_item' => __( 'Update Author', 'lp-guest-authors' ),
			'add_new_item' => __( 'Add New Author', 'lp-guest-authors' ),
			'new_item_name' => __( 'New Author Name', 'lp-guest-authors' ),
			'not_found' => __( 'No records found.', 'lp-guest-authors' ),
			'menu_name' => __( 'Guest Authors', 'lp-guest-authors' ),
			'add_or_remove_items' => __( 'Add or remove authors', 'lp-guest-authors' ),
			'choose_from_most_used' => __( 'Choose from most used authors', 'lp-guest-authors' ),
			'separate_items_with_commas' => __( 'Separate authors with commas', 'lp-guest-authors' ),
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'hierarchical' => false,
			'show_admin_column' => true,
			'query_var' => false,
			'rewrite' => false,
		);

		$taxonomies_to_register['guest_author'] = array(
			'args' => $args,
			'synced_object_type' => array( 'user' ),
			'object_type' => array( 'post' ),
		);

		/** register taxonomies */
		foreach ( $taxonomies_to_register as $tax => $settings ) {
			register_taxonomy( $tax, $settings['object_type'], $settings['args'] );

			if ( !isset( $settings['synced_object_type'] ) || !$settings['synced_object_type'] ) {
				continue;
			}

			foreach ( (array) $settings['synced_object_type'] as $object_type ) {
				$this->objects_to_taxonomies[$object_type][] = $tax;
			}
		}
	}

	/**
	 * Add guest authors to the_author
	 *
	 * @param string $author_name
	 * @return string
	 */
	public function filter_the_author( $author_name ) {

		return $this->append_guest_authors( $author_name, false );
	}

	/**
	 * Add guest authors to the_author_posts_link.
	 *
	 * @param string $link
	 * @return string
	 */
	public function filter_the_author_posts_link( $link ) {

		return $this->append_guest_authors( $link, true );
	}

	/**
	 * Filter the_author and the_author_posts_link
	 *
	 * @param string $author_string
	 * @param boolean $is_link
	 * @return string
	 */
	public function append_guest_authors( $author_string, $is_link = false ) {

		$post = get_post();

		// get all guest authors
		$terms = get_the_terms( $post, 'guest_author' );

		// exit early
		if ( !$post || !has_guest_authors() || !is_array( $terms ) ) {
			return $author_string;
		}

		// blank start
		$author_string = '';

		// loop authors
		$terms_count = count( $terms );
		$i = 0;

		foreach ( $terms as $term ) {
			
			$i++;

			$is_single_guest = ( 1 === $i && 1 === $terms_count );
			$is_last_guest = ( $i === $terms_count );
			$is_second_to_last_guest = ( $i === $terms_count - 1 );

			// separator
			if ( $is_single_guest || $is_last_guest ) {
				$sep = '';
			}
			else if ( $is_second_to_last_guest ) {
				$sep = _x( ' &amp; ', 'guest author separator', 'lp-guest-authors' );
			}
			else {
				$sep = ', ';
			}

			// append linked name
			if ( $is_link ) {
				$author_string .= sprintf( '<a href="%1$s" title="%2$s" rel="author">%3$s</a>', esc_url( get_term_link( $term, $term->taxonomy ) ), esc_attr( sprintf( __( 'Posts by %s' ), $term->name ) ), $term->name );
			}
			// append name only
			else {
				$author_string .= $term->name;
			}

			// append separator
			$author_string .= $sep;
		}

		return $author_string;
	}

	/**
	 * Plugin activation and deactivation
	 *
	 * @return void
	 */
	public static function activate() {
		flush_rewrite_rules();
	}

}

/**
 * Register actitavion/deactivation hooks.
 */
register_activation_hook( LP_GUEST_AUTHORS_PLUGIN_FILE, array( 'LP_Plugin_Guest_Authors', 'activate' ) );
register_deactivation_hook( LP_GUEST_AUTHORS_PLUGIN_FILE, array( 'LP_Plugin_Guest_Authors', 'activate' ) );

/**
 * Instantiate.
 */
LP_Plugin_Guest_Authors::get_instance();
