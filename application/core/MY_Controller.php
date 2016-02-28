<?php

/**
 * Class MY_Controller
 */
class MY_Controller extends CI_Controller {

    var $requireLogin   = FALSE;
    var $viewSubFolder  = '/';

    public function __construct()
    {
        parent::__construct();

        if(!$this->input->is_cli_request()) {
            $this->load->library('session');

            if (defined('ENVIRONMENT')) {
                if(ENVIRONMENT == 'development') {
                    $this->output->enable_profiler(true);

                    $this->carabiner->css('profiler.css');
                    $this->carabiner->js('profiler.js');
                }
            }
        }

        $this->CI =& get_instance();

        // Check Login
        if($this->requireLogin) {
            if(!isset($this->session->userdata['user_id'])) {
                redirect(LOGIN_URL);
            } else {
                $this->data['user'] = new User_model();
                $this->data['user'] = $this->data['user']->getById($this->session->userdata['user_id']);
            }
        }

        // Load CSS
        $this->carabiner->css('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css'); // Bootstrap
        $this->carabiner->css('main.css'); // Bootstrap

        // Load JS
        $this->carabiner->js('https://code.jquery.com/jquery-2.2.1.min.js'); // jQuery
        $this->carabiner->js('https://code.jquery.com/ui/1.11.4/jquery-ui.min.js'); // jQuery UI
        $this->carabiner->js('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'); // Bootstrap

        // Get Flash message
        $this->getFlash();
    }

    /**
     * Set a flash message in session
     */
    public function setFlash($value, $item = 'info')
    {
        $this->session->set_flashdata($item, $value);
    }

    /**
     * Get a flash message to display
     */
    public function getFlash()
    {
        $this->data['flash'] = array();

        if($this->session->flashdata('info'))
            $this->data['flash']['info'] = $this->session->flashdata('info');
        if($this->session->flashdata('warning'))
            $this->data['flash']['warning'] = $this->session->flashdata('warning');
        if($this->session->flashdata('success'))
            $this->data['flash']['success'] = $this->session->flashdata('success');
        if($this->session->flashdata('danger'))
            $this->data['flash']['danger'] = $this->session->flashdata('danger');
    }

    /**
     * Check if id is numeric
     */
    public function checkId($id, $uri = '')
    {
        if($id == '' || !(is_numeric($id))) {
            // @TODO : Set a flash message if necessary
            redirect($uri);
        }
    }

    /**
     * Display view
     */
    public function view($view)
    {
        $this->load->view($this->viewFolder . 'header', $this->data);
        $this->load->view($this->viewFolder . $view,    $this->data);
        $this->load->view($this->viewFolder . 'footer', $this->data);
    }
}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */