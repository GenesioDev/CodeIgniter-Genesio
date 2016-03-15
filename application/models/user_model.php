<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class User_model
 *
 * Model for users
 */
class User_model extends MY_Model
{
    /**
     * Table used by this model
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Extend create function to add Salt and Encrypt Password
     *
     * @param array $escaped_fields
     * @param array $not_escaped_fields
     * @return bool
     */
    public function add()
    {
        if($this->checkUniqField('email')) {
            $this->generateSalt()
                ->setEncryptPassword()
                ->save();;
        } else {
            return FALSE;
        }
    }

    /**
     * Looks for the email in database and compares passwords
     *
     * @param $email
     * @param $password
     * @return User object or FALSE if fail
     */
    public function checkLogin($email, $password)
    {
        // search the user with this email
        $user = $this->get(array('email' => $email));

        // If we find at least one user
        if ($user->exists()) {
            $givenPassword = $this->encryptPassword($password, $user->salt);
            if ($givenPassword == $user->password) {
                return $user;
            }
        }

        return FALSE;
    }

    /**
     * Looks for the user and compares passwords before update data
     *
     * @param $userId
     * @param $oldPassword
     * @param $newPassword
     * @return bool
     */
    public function changePassword($newPassword)
    {
        $this->password = $newPassword;
        $this->setEncryptPassword();

        return $this;
    }

    /**
     * Creates a salt from an encrypted uniqid()
     *
     * @return string
     */
    protected function generateSalt()
    {
        $this->salt = sha1(uniqid('salt', TRUE));

        return $this;
    }

    /**
     * Gives the password like it looks in
     *
     * @param $password
     * @param $salt
     * @return string
     */
    protected function encryptPassword($password, $salt)
    {
        return sha1($password.$salt);
    }

    /**
     * Set encrypted password
     *
     * @return object
     */
    protected function setEncryptPassword()
    {
        $this->password = $this->encryptPassword($this->password, $this->salt);

        return $this;
    }
}