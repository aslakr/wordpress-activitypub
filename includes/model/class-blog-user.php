<?php
namespace Activitypub\Model;

use WP_Query;
use Activitypub\Signature;
use Activitypub\Collection\Users;

use function Activitypub\is_single_user;
use function Activitypub\is_user_disabled;

class Blog_User extends User {
	/**
	 * The User-ID
	 *
	 * @var int
	 */
	protected $_id = Users::BLOG_USER_ID; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * The User-Type
	 *
	 * @var string
	 */
	protected $type = 'Group';

	/**
	 * Is Account discoverable?
	 *
	 * @var boolean
	 */
	protected $discoverable = true;

	public static function from_wp_user( $user_id ) {
		if ( is_user_disabled( $user_id ) ) {
			return new WP_Error(
				'activitypub_user_not_found',
				\__( 'User not found', 'activitypub' ),
				array( 'status' => 404 )
			);
		}

		$object = new static();
		$object->_id = $user_id;

		return $object;
	}

	/**
	 * Get the User-Name.
	 *
	 * @return string The User-Name.
	 */
	public function get_name() {
		return \esc_html( \get_bloginfo( 'name' ) );
	}

	/**
	 * Get the User-Description.
	 *
	 * @return string The User-Description.
	 */
	public function get_summary() {
		return \wpautop(
			\wp_kses(
				\get_bloginfo( 'description' ),
				'default'
			)
		);
	}

	/**
	 * Get the User-Url.
	 *
	 * @return string The User-Url.
	 */
	public function get_url() {
		return \esc_url( \trailingslashit( get_home_url() ) . '@' . $this->get_preferred_username() );
	}

	/**
	 * Returns the User-URL with @-Prefix for the username.
	 *
	 * @return string The User-URL with @-Prefix for the username.
	 */
	public function get_at_url() {
		return \esc_url( \trailingslashit( get_home_url() ) . '@' . $this->get_preferred_username() );
	}

	/**
	 * Generate a default Username.
	 *
	 * @return string The auto-generated Username.
	 */
	public static function get_default_username() {
		// check if domain host has a subdomain
		$host = \wp_parse_url( \get_home_url(), \PHP_URL_HOST );
		$host = \preg_replace( '/^www\./i', '', $host );

		/**
		 * Filter the default blog username.
		 *
		 * @param string $host The default username.
		 */
		return apply_filters( 'activitypub_default_blog_username', $host );
	}

	/**
	 * Get the preferred User-Name.
	 *
	 * @return string The User-Name.
	 */
	public function get_preferred_username() {
		$username = \get_option( 'activitypub_blog_user_identifier' );

		if ( $username ) {
			return $username;
		}

		return self::get_default_username();
	}

	/**
	 * Get the User-Icon.
	 *
	 * @return array The User-Icon.
	 */
	public function get_icon() {
		// try site icon first
		$icon_id = get_option( 'site_icon' );

		// try custom logo second
		if ( ! $icon_id ) {
			$icon_id = get_theme_mod( 'custom_logo' );
		}

		$icon_url = false;

		if ( $icon_id ) {
			$icon = wp_get_attachment_image_src( $icon_id, 'full' );
			if ( $icon ) {
				$icon_url = $icon[0];
			}
		}

		if ( ! $icon_url ) {
			// fallback to default icon
			$icon_url = plugins_url( '/assets/img/wp-logo.png', ACTIVITYPUB_PLUGIN_FILE );
		}

		return array(
			'type' => 'Image',
			'url'  => esc_url( $icon_url ),
		);
	}

	/**
	 * Get the User-Header-Image.
	 *
	 * @return array|null The User-Header-Image.
	 */
	public function get_header_image() {
		if ( \has_header_image() ) {
			return array(
				'type' => 'Image',
				'url'  => esc_url( \get_header_image() ),
			);
		}

		return null;
	}

	public function get_published() {
		$first_post = new WP_Query(
			array(
				'orderby' => 'date',
				'order'   => 'ASC',
				'number'  => 1,
			)
		);

		if ( ! empty( $first_post->posts[0] ) ) {
			$time = \strtotime( $first_post->posts[0]->post_date_gmt );
		} else {
			$time = \time();
		}

		return \gmdate( 'Y-m-d\TH:i:s\Z', $time );
	}

	public function get_attachment() {
		return array();
	}

	public function get_canonical_url() {
		return \home_url();
	}

	/**
	 * Get the type of the object.
	 *
	 * If the Blog is in "single user" mode, return "Person" insted of "Group".
	 *
	 * @return string The type of the object.
	 */
	public function get_type() {
		if ( is_single_user() ) {
			return 'Person';
		} else {
			return $this->type;
		}
	}
}
