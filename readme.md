*******************
Author: hoanglongbkit@gmail.com
Description: add some features of django filter to Codeignitor
*******************

*******************
What is CodeIgniter & Django filter function
*******************

CodeIgniter with Django filter function is very convenience for query data.
This function is not fully working same with real Django filter, or not support Q query of Django.
 

*******************
How to use
*******************

- 1. Add sample data: catagory.sql > connect to database> modify: application>config>database.php
- 2. Define table names in: application>config>constants.php
```
$all_data_tables =[];
$all_data_tables[] ="catagory";
```

- 3. Add application>models> All_model.php
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
        public $catagory_id=null;

        public function __construct()
        {
            parent::__construct();
            $this->set_table("catagory");
            $this->set_field_default("name");
            $this->set_join_tables("catagory","catagory_id");
            $this->load->database();
        }
    }
?>
```


- 4. Modify in application> controllers> Welcome>index()
```
$this->load->model('all_model');
$obj_catagory = new Catagory_model();
$cats = $obj_catagory->filter(array("catagory_id"=>null))->all();
foreach ($cats as $cat)
{
    echo "<br>".$cat->name;
    
    // when set_join_tables => the field catagory_id has been removed => replace with object "catagory"
    // can not access $cat->catagory_id
    echo "<br>".$cat->catagory->id;  
}
```

**************************
Filter features
**************************
- Filter data and get all records
```
$obj_catagory->filter(array("catagory_id"=>null))->all(); 
```

- Filter data and get one record
```
$obj_catagory->filter(array("catagory_id"=>null))->one();
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
$obj_catagory->filter(array("catagory_id"=>null))->limit(2)->all(); 
```

- Order result
```
$obj_catagory->filter(array("catagory_id"=>null))->order_by(array("name"=>"asc", "id"=>"asc"))->all();
```

- Filter children_set: select in table "catagory" where "catagory_id"= $catagory_id
```
$obj_catagory = new Catagory_model();
$cat = $obj_catagory->filter(array("name"=>"car"))->one();
if ($cat != null)
{
    $cats = $cat->children_set("catagory")->all();
    foreach ($cats as $cat)
    {
        echo "<br>".$cat->name;
    }
}
```

- Filter connect_set: select in table "catagory" where "catagory_id"= $cat->catagory_id
```
$obj_catagory = new Catagory_model();
$cat = $obj_catagory->filter(array("name"=>"car"))->one();
if ($cat != null)
{
    $cats = $cat->connect_set("catagory")->all();
    foreach ($cats as $cat)
    {
        echo "<br>".$cat->name." - catagory_id:".$cat->catagory_id;
    }
}
```

- get default value
```
$cats = $obj_catagory->filter(array("name"=>"car"))->all();
foreach($cats as $cat)
{
    echo $cat->value();
}
```

- Extra select

- set() or save()
```
$obj_catagory->name = "car";
$obj_catagory->save();
```

- update
```
$cat = $obj_catagory->filter(array("name"=>"car"))->one();
if ($cat != null){
    $cat->name = "my_car";
    $cat->update();
}
```

- update for connect table by id
```
$cat = $obj_catagory->filter(array("name"=>"car"))->one();
if ($cat != null){
    $cat->catagory_id = 1;
    $cat->update();
}
```

- update for connect table by object
```
$obj_catagory = new Catagory_model();
$cat = $obj_catagory->filter(array("name"=>"car"))->one();
if ($cat != null){
    $obj_catagory = new Catagory_model();
    $cat->catagory = $obj_catagory->filter(array("name"=>"my_car"))->one();
    $cat->update();
}
```

- delete
```
//delete one
$cat = $obj_catagory->filter(array("name"=>"my_car"))->one();
if ($cat != null)
{
    $cat->delete();
}

//delete all
$cats = $obj_catagory->filter(array("name"=>"my_car"))->all();
if (count($cats) > 0)
{
    foreach ($cats as $cat)
    {
        $cat->delete();
    }
}
```

- group_by
- count_all_results
```
$num = $obj_catagory->filter(array("name"=>"car"))->count_all_results();
```

- get() = filter()


