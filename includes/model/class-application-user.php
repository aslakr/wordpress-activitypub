<?php
namespace Activitypub\Model;

use WP_Query;
use Activitypub\Signature;
use Activitypub\Collection\Users;

use function Activitypub\get_rest_url_by_path;

class Application_User extends Blog_User {
	/**
	 * The User-ID
	 *
	 * @var int
	 */
	protected $_id = Users::APPLICATION_USER_ID; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * The User-Type
	 *
	 * @var string
	 */
	protected $type = 'Application';

	/**
	 * If the User is discoverable.
	 *
	 * @var boolean
	 */
	protected $discoverable = false;

	/**
	 * Get the User-Url.
	 *
	 * @return string The User-Url.
	 */
	public function get_url() {
		return get_rest_url_by_path( 'application' );
	}

	public function get_name() {
		return 'application';
	}

	public function get_preferred_username() {
		return $this::get_name();
	}

	public function get_followers() {
		return null;
	}

	public function get_following() {
		return null;
	}

	public function get_attachment() {
		return array();
	}

	public function get_featured_tags() {
		return array();
	}

	public function get_featured() {
		return array();
	}
}
