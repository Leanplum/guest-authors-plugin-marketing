<?php

if ( !function_exists( 'has_guest_authors' ) ) {

	/**
	 * Check if a post has more than one author.
	 *
	 * @return boolean
	 */
	function has_guest_authors() {

		$post = get_post();

		// no post
		if ( ! $post ) {
			return false;
		}

		// grab all post authors (guest_author terms)
		$guest_authors = get_the_terms( $post->ID, 'guest_author' );

		// no `guest_author` terms
		if ( ! is_array( $guest_authors ) ) {
			return false;
		}

		// count co-authors
		if ( count( $guest_authors ) > 0 ) {
			return true;
		} else {
			return false;
		}

	}
}