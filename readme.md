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
Filter features
**************************
- Filter data and get all records
```
$obj_catagory->filter(array("cat_id"=>null))->all(); 
```

- Filter data and get one record
```
$obj_catagory->filter(array("cat_id"=>null))->one();
```

- Filter icontain, notIcontain, beginWith, notBeginWith, endWith, notEndWith
```
$obj_catagory->filter(array("name__icontain"=>"car"))->all();
$obj_catagory->filter(array("name__notIcontain"=>"car"))->all();
$obj_catagory->filter(array("name__beginWith"=>"ca"))->all();
$obj_catagory->filter(array("name__notBeginWith"=>"ca"))->all();
$obj_catagory->filter(array("name__endWith"=>"ar"))->all();
$obj_catagory->filter(array("name__notEndWith"=>"ar"))->all();
```

- Filter connect table
```
$obj_catagory->filter(array("catagory__name"=>"car"))->all();
$obj_catagory->filter(array("catagory__name__icontain"=>"car"))->all();
```

- Fitler of Filter
```
$obj_catagory->filter(array("name__icontain"=>"toyota"))->filter(array("catagory__name"=>"car"))->all();
```

- Filter or condition: due to current not support Q filter same like django so Filter or is using for query or condition
```
$obj_catagory->filter_or(array("catagory__name__icontain"=>"car"), "name_icontain"=>"toyota")->all();
```

- Filter with array values
```
$obj_catagory->filter(array("id"=>[0,1]))->all();
$obj_catagory->filter_or(array("id"=>[0,1], "name"=>"phone"))->all();
```

- Limit query
```
$obj_catagory->filter(array("cat_id"=>null))->limit(2)->all(); 
```

- Order result
```
$obj_catagory->filter(array("cat_id"=>null))->order_by(array("name"=>"asc", "id"=>"asc"))->all();
```

- Filter children_set
- Filter connect_set
- get default value
- Extra select
- set
- update
- delete
- group_by
- count_all_results
- get() = filter()


