<?php
/**
* Theme
*/
class Theme
{
	// Get Image Thumbnail
	public function get_thumbnail( $post_id, $size )
	{
		return $this->get_the_images( $post_id, 'thumbnail', $size );
	}

	// Get Image
	public function get_image( $img_id, $size )
	{
		$img_id = explode( ',', $img_id );
		for ( $i=0; $i < count( $img_id ); $i++ ) {
			if ( $img_id[ $i ] ) {
				return $this->get_the_images( $img_id[ $i ], 'image', $size );
			}
		}
	}

	protected function get_the_images( $id, $type, $size )
	{
		$img_id = ( $type == 'thumbnail' ) ? get_post_thumbnail_id( $id ) : $id;

		$img_src 	= wp_get_attachment_image_src( $img_id, $size );
		$img_large 	= wp_get_attachment_image_src( $img_id, 'large' );

		$image->img->src 	= $img_src[ 0 ];
		$image->img->large 	= $img_large[ 0 ];
		$image->img->width 	= $img_src[ 1 ];
		$image->img->height = $img_src[ 2 ];
		$image->img->title 	= get_the_title( $img_id );

		return $image;
	}

	// Menu
	public function menu( $menu_name )
	{
		if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
			$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
			$menu_items = wp_get_nav_menu_items( $menu->term_id );
			$i = 0;
			foreach ( ( array ) $menu_items as $key => $menu_item ) {
				$id = $menu_item->ID;

				$mn->$id->url 	= esc_url( $menu_item->url );
				$mn->$id->title	= $menu_item->title;

				$i++;
			}
			$mn->count = $i;

			return $mn;
		}
	}
}
