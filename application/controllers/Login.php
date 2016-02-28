<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

    var $viewFolder     = '/login/';
    var $requireLogin   = FALSE;

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        if($this->input->post()) {

            $user = new User_model();
            $user = $user->checkLogin($this->input->post('email'), $this->input->post('password'));

            if ($user) {
                $this->CI->session->set_userdata('user_id', $user->id);
                redirect('/');
            }
        }

        $this->view('home');
    }

    /**
     * Logout user
     */
    public function logout()
    {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('googleAccessToken');
        $this->setFlash('Vous êtes déconnecté');

        redirect('/');
    }
}