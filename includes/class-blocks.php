<?php
namespace Activitypub;

use Activitypub\Collection\Followers;
use Activitypub\is_user_type_disabled;

class Blocks {
	public static function init() {
		// this is already being called on the init hook, so just add it.
		self::register_blocks();
		\add_action( 'wp_enqueue_scripts', array( self::class, 'add_data' ) );
		\add_action( 'enqueue_block_editor_assets', array( self::class, 'add_data' ) );
	}

	public static function add_data() {
		$context = is_admin() ? 'editor' : 'view';
		$followers_handle = 'activitypub-followers-' . $context . '-script';
		$follow_me_handle = 'activitypub-follow-me-' . $context . '-script';
		$data = array(
			'namespace' => ACTIVITYPUB_REST_NAMESPACE,
			'enabled' => array(
				'site' => ! is_user_type_disabled( 'blog' ),
				'users' => ! is_user_type_disabled( 'user' ),
			),
		);
		$js = sprintf( 'var _activityPubOptions = %s;', wp_json_encode( $data ) );
		\wp_add_inline_script( $followers_handle, $js, 'before' );
		\wp_add_inline_script( $follow_me_handle, $js, 'before' );
	}

	public static function register_blocks() {
		\register_block_type_from_metadata(
			ACTIVITYPUB_PLUGIN_DIR . '/build/followers',
			array(
				'render_callback' => array( self::class, 'render_follower_block' ),
			)
		);
		\register_block_type_from_metadata(
			ACTIVITYPUB_PLUGIN_DIR . '/build/follow-me',
			array(
				'render_callback' => array( self::class, 'render_follow_me_block' ),
			)
		);
	}

	private static function get_user_id( $user_string ) {
		if ( is_numeric( $user_string ) ) {
			return absint( $user_string );
		}
		// any other non-numeric falls back to 0, including the `site` string used in the UI
		return 0;
	}

	/**
	 * Render the follow me block.
	 * @param array $attrs The block attributes.
	 * @return string The HTML to render.
	 */
	public static function render_follow_me_block( $attrs ) {
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'aria-label' => __( 'Follow me on the Fediverse', 'activitypub' ),
				'class'      => 'activitypub-follow-me-block-wrapper',
				'data-attrs' => wp_json_encode( $attrs ),
			)
		);
		// todo: render more than an empty div?
		return '<div ' . $wrapper_attributes . '></div>';
	}

	public static function render_follower_block( $attrs ) {
		$followee_user_id = self::get_user_id( $attrs['selectedUser'] );
		$per_page = absint( $attrs['per_page'] );
		$followers = Followers::get_followers( $followee_user_id, $per_page );
		$title = $attrs['title'];
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'aria-label' => __( 'Fediverse Followers', 'activitypub' ),
				'class'      => 'activitypub-follower-block',
				'data-attrs' => wp_json_encode( $attrs ),
			)
		);

		$html = '<div ' . $wrapper_attributes . '>';
		if ( $title ) {
			$html .= '<h3>' . $title . '</h3>';
		}
		$html .= '<ul>';
		foreach ( $followers as $follower ) {
			$html .= '<li>' . self::render_follower( $follower ) . '</li>';
		}
		// We are only pagination on the JS side. Could be revisited but we gotta ship!
		$html .= '</ul></div>';
		return $html;
	}

	public static function render_follower( $follower ) {
		$external_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="components-external-link__icon css-rvs7bx esh4a730" aria-hidden="true" focusable="false"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg>';
		$template =
			'<a href="%s" title="%s" class="components-external-link activitypub-link" target="_blank" rel="external noreferrer noopener">
				<img width="40" height="40" src="%s" class="avatar activitypub-avatar" />
				<span class="activitypub-actor">
					<strong class="activitypub-name">%s</strong>
					<span class="sep">/</span>
					<span class="activitypub-handle">@%s</span>
				</span>
				%s
			</a>';

		$data = $follower->to_array();

		return sprintf(
			$template,
			esc_url( $data['url'] ),
			esc_attr( $data['name'] ),
			esc_attr( $data['icon']['url'] ),
			esc_html( $data['name'] ),
			esc_html( $data['preferredUsername'] ),
			$external_svg
		);
	}
}
