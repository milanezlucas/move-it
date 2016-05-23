<?php
// Widgets on page
class MI_Widgets extends MI_Forms
{
	protected $wdg_posts;
	protected $wdg_widgets;

	function __construct()
	{
		add_action( 'admin_init',   array( $this, 'widgets_init' ) );
        add_action( 'save_post',    array( $this, 'widgets_save' ) );
	}

	public function widgets_init()
	{
		global $wpdb;
        $wpdb->widgets = $wpdb->prefix . 'widgets';
        $table_exists = get_option( MI_PREFIX . 'widgets_exists' );

        if ( !$table_exists ) {
            add_option( MI_PREFIX . 'widgets_exists', true );
            $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->widgets}` (
                `id_widget` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `widget_id` varchar(255) NOT NULL,
                `merges` longtext NOT NULL,
                PRIMARY KEY (`id_widget`)
            ) ENGINE=InnoDB";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }

        for ( $i=0; $i < count( $this->wdg_posts ); $i++ ) {
			add_meta_box( 'widget_' . $this->wdg_posts[ $i ], 'Widgets', array( $this, 'widget_edit' ), $this->wdg_posts[ $i ], 'normal', 'high', '' );
		}
	}

	public function add_post_widget( $posts )
	{
		$this->wdg_posts = $posts;
	}

	public function widget_edit( $post )
	{
		$widgets_page = get_post_meta( $post->ID, MI_PREFIX . 'widgets_order', true );
		$widgets_page = $widgets_page ? explode( ',', $widgets_page ) : '';

		$html .= '
			<input type="hidden" name="widgets_order" id="widgets_order" value="' . get_post_meta( $post->ID, MI_PREFIX . 'widgets_order', true ) . '">
			<div class="sortable-wrap">
				<div class="column">
					<div class="portlet" id="0" style="display:none"><form class="form-widget"></form></div>
		';
		for ( $i=0; $i < count( $widgets_page ); $i++ ) {
			if ( $widgets_page[ $i ] ) {
				$html .= $this->mount_widget( $widgets_page[ $i ] );
			}
		}
		$html .= '
				</div>
				<p class="text-right"><a href="#TB_inline?width=100&height=550&inlineId=add-widget" class="thickbox">+ Adicionar Widget</a></p>
			</div>

			<div id="add-widget" style="display: none;">
                <h1>Adicionar Widgets</h1>
        ';
        foreach ( $this->wdg_widgets as $id => $wdg ) {
        	$html .= '
				<div class="portlet portlet-add" id="' . $id . '">
					<div class="portlet-header">' . $wdg->title . '</div>
				</div>
        	';
        }
        $html .= '
            </div>
		';

		echo $html;
	}

	public function widgets_save( $post_id )
	{
		if ( $_POST[ 'widgets_order' ] ) {
			add_post_meta( $post_id, MI_PREFIX . 'widgets_order', $_POST[ 'widgets_order' ], true ) or update_post_meta( $post_id, MI_PREFIX . 'widgets_order', $_POST[ 'widgets_order' ] );
		}
	}

	// Register Widgets
	public function register_widget( $title, $id, $fields )
	{
		$this->wdg_widgets->$id->title 		= $title;
		$this->wdg_widgets->$id->fields 	= $fields;
	}

	public static function widget_name( $id )
	{
		$date =  mktime( date( 'H' ), date( 'i' ), date( 's' ), date( 'm' ), date( 'd' ), date( 'Y' ) );

		return $date . '_' . MI_PREFIX . $id;
	}

	protected function mount_widget( $widget_id )
	{
		$id = explode( MI_PREFIX, $widget_id );
		$id = $id[ 1 ];

		$html .= '
			<div class="portlet" id="' . $widget_id . '">
				<div class="portlet-header">' . $this->wdg_widgets->$id->title . '</div>
				<div class="portlet-content">
					<form class="form-widget">
						<input type="hidden" name="widget_id" id="widget_id" value="' . $widget_id . '">
						<table class="form-table">
							<tbody>
		';
		foreach ( $this->wdg_widgets->$id->fields as $id => $field ) {
			$form = $this->field( $id, $field, $this->get_widget_meta( $widget_id, $id ), $widget_id );

			$html .= '
						<tr>
							<th scope="row"><label for="' . $form->name . '">' . $form->label . '</label></th>
							<td>
								' . $form->field . '
			';
			$html .= $form->desc ? '<p class="description">' . $form->desc . '</p>' : '';
			$html .= '
							</td>
						</tr>
			';
		}
		$html .= '
							</tbody>
						</table>
						<p class="submit"><input name="submit" id="submit" class="button button-primary" value="Salvar alterações" type="submit"></p>
						<p class="text-left"><a href="#" class="exclude-widget" data-widget="' . $widget_id . '">Excluir Widget</a></p>
					</form>
				</div>
			</div>
		';

		return $html;
	}

	// Widgets Utils
	public function get_widget_meta( $widget_id, $field )
	{
		global $wpdb;
		$rs_query_widget = $wpdb->get_results( "SELECT * FROM " . $wpdb->widgets . " WHERE widget_id='" . esc_sql( $widget_id ) . "' ORDER BY id_widget DESC LIMIT 1" );
		foreach ( $rs_query_widget as $rs_widget ) {}

		$widget_meta = json_decode( $rs_widget->merges );

		return $widget_meta->$field;
	}

	protected function widget_existis( $widget_id )
	{
		global $wpdb;
		$rs_query_widget = $wpdb->get_results( "SELECT * FROM " . $wpdb->widgets . " WHERE widget_id='" . esc_sql( $widget_id ) . "'" );

		return $rs_query_widget;
	}

	public function widget_save( $widget_id, $merges )
	{
		global $wpdb;

		if ( $this->widget_existis( $widget_id ) ) {
			$wpdb->update(
				$wpdb->widgets,
				array(
					'merges' => $merges,
				),
				array( 'widget_id' => $widget_id ),
				array(
					'%s',
				),
				array( '%s' )
			);
		} else {
			$wpdb->insert(
				$wpdb->widgets,
				array(
					'widget_id' => $widget_id,
					'merges' 	=> $merges
				),
				array(
					'%s',
					'%s'
				)
			);
		}
	}

	public function exclude_widget( $widget_id )
	{
		global $wpdb;
		$wpdb->delete( $wpdb->widgets, array( 'widget_id' => $widget_id ), array( '%s' ) );
	}

	// Widget Front
	public function the_widget()
	{
		global $post;

		$widgets_page = get_post_meta( $post->ID, MI_PREFIX . 'widgets_order', true );
		$widgets_page = $widgets_page ? explode( ',', $widgets_page ) : '';

		for ($i=0; $i < count( $widgets_page ); $i++) {
			if ( $widgets_page[ $i ] ) {
				$id = explode( MI_PREFIX, $widgets_page[ $i ] );
				$id = $id[ 1 ];

				echo $id( $this->get_the_widget( $widgets_page[ $i ] ) );
			}
		}
	}

	protected function get_the_widget( $widget_id )
	{
		global $wpdb;

		$rs_query_widget = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "widgets WHERE widget_id='" . esc_sql( $widget_id ) . "' ORDER BY id_widget DESC LIMIT 1" );
		foreach ( $rs_query_widget as $rs_widget ) {}

		return json_decode( $rs_widget->merges );
	}
}
?>
