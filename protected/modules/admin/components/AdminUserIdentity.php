<?php

/**
 * AdminUserIdentity represents the data needed to identity a admin.
 * It contains the authentication method that checks if the provided
 * data can identity the admin.
 */
class AdminUserIdentity extends CUserIdentity
{
	// Need to store the user's ID:
	private $_id;
	/**
	 * Authenticates a admin.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$users = Admins::model()->findByAttributes(array('email'=>$this->username));
		
		//echo "<pre>";print_r($users);
		//echo $users->id; exit;
		
		if ($users===null) { // No user found!
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		} else if ($users->password !== base64_encode($this->password) ) { // Invalid password!
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
			//In the first conditional, if $user has no value, then no records were found, so the email address was incorrect. In the second conditional, the stored password is compared against the SHA1() version of the submitted password. This assumes the record’s password was stored in a SHA1()-encrypted format. If neither of these conditionals are true, then everything is okay:
		}else{
			$this->errorCode=self::ERROR_NONE;
			$this->_id = $users->id;
		}
		
		return !$this->errorCode;
	}
	
	public function getId()
	{
		return $this->_id;
	}
}