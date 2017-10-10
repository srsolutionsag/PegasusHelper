<?php

require_once('./Services/ActiveRecord/class.ActiveRecord.php');

/**
 * Class UserToken is the entity class for the custom user token.
 * The related database table is created in
 * {@code REST/sql/dbupdate.php}.
 *
 * The REST Plugin creates the table and uses it to generate tokens.
 * ILIASAppModel#createToken(int)
 * located in: {@code REST/RESTController/extensions/ilias_app_v1/models/ILIASAppModel.php}
 *
 * The REST Plugin operates directly with {@code ilDB}, because it has no complex statements.
 * However, the {@code TokenChecker} has complex statements, therefore the Active Record is used here.
 *
 *
 * @author  Nicolas Märchy <nm@studer-raimann.ch>
 * @version 1.0.0
 *
 */
class UserToken extends ActiveRecord {

	public static function returnDbTableName() {
		return 'ui_uihk_rest_token';
	}

	/**
	 * @var int
	 *
	 * @con_is_primary true
	 * @con_is_unique true
	 * @con_has_field true
	 * @con_fieldtype integer
	 * @con_length 4
	 */
	protected $user_id;

	/**
	 * @var string
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length 128
	 */
	protected $token;
	/**
	 * @con_has_field true
	 * @con_fieldtype timestamp
	 */
	protected $expires;


	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}


	/**
	 * @param int $user_id
	 */
	public function setUserId($user_id) {
		$this->user_id = $user_id;
	}


	/**
	 * @return string
	 */
	public function getToken() {
		return $this->token;
	}


	/**
	 * @param string $token
	 */
	public function setToken($token) {
		$this->token = $token;
	}


	/**
	 * @return string
	 */
	public function getExpires() {
		return $this->expires;
	}


	/**
	 * @param string $expires
	 */
	public function setExpires($expires) {
		$this->expires = $expires;
	}
}