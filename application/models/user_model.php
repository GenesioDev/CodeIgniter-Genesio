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
    public function create($escaped_fields = array(), $not_escaped_fields = array())
    {
        // check if $email exist
        if ($this->db->where(array('email' => $escaped_fields['email']))
                ->count_all_results($this->table) > 0 ) {
            return false;
        }

        // generate salt
        $escaped_fields['salt'] = $this->generateSalt();
        // encrypt password
        $escaped_fields['password'] = $this->encryptPassword($escaped_fields['password'], $escaped_fields['salt']);

        return parent::create($escaped_fields, $not_escaped_fields);
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
        $user = $this->get(array('email', $email));

        // If we find at least one user (but we must not find more)
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
    public function changePassword($userId, $oldPassword, $newPassword)
    {
        $user = $this->getById($userId);
        if ($user->exists()) {
            // If the old password is good
            if ($this->encryptPassword($oldPassword, $user->salt) == $user->password) {
                $this->db->set('password', $this->encryptPassword($newPassword, $user->salt));
                $this->db->where('id', (int)$user->id);
                return $this->db->update($this->table);
            }
        }

        return false;
    }

    /**
     * Creates a salt from an encrypted uniqid()
     *
     * @return string
     */
    protected function generateSalt()
    {
        return sha1(uniqid('salt', TRUE));
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
}