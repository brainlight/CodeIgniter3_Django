<?php
    class All_model extends MY_Django_Model 
    {
        public function __construct()
        {
            parent::__construct();
        }
    }
    
    class Catagory_model extends MY_Django_Model {

        public $id = null;
        public $name=null;
        public $cat=null;

        public function __construct()
        {
            parent::__construct();
            $this->set_table("catagory");
            $this->set_field_default("name");
            $this->set_join_tables("catagory","cat_id");
            $this->load->database();
        }
    }
?>
