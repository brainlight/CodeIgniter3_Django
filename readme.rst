###################
What is CodeIgniter & Django filter function
###################

CodeIgniter with Django filter function is very convenience for query data.
This function is not fully working same with real Django filter, or not support Q query of Django.
 

*******************
How to use
*******************

- Add sample data
- Add models> All_model.php
```
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
```

- Modify in controllers> Welcome>index()
```
$this->load->model('all_model');
$obj_catagory = new Catagory_model();
$cats = $obj_catagory->filter(array("cat_id"=>null))->all();
foreach ($cats as $cat)
{
    echo "<br>".$cat->name;
}
```

**************************
Filter advance query
**************************

