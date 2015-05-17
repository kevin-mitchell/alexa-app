<?php
/**
 * Created by PhpStorm.
 * User: shoelessone
 * Date: 5/14/15
 * Time: 8:25 PM
 */

namespace Develpr\AlexaApp;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Contracts\Auth\Authenticatable;


class AlexaUser extends Eloquent implements Authenticatable{

	protected $table = 'alexa_users';

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->alexa_user_id;
		// TODO: Implement getAuthIdentifier() method.
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
		// TODO: Implement getAuthPassword() method.
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return "HI";
		// TODO: Implement getRememberToken() method.
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		return "HI";
		// TODO: Implement setRememberToken() method.
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return "HI";
		// TODO: Implement getRememberTokenName() method.
	}

} 