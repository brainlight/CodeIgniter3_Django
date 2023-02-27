<?php defined('BASEPATH') OR exit('No direct script access allowed');
    
class MY_Model extends CI_Model{}

class MY_Django_Model extends CI_Model{
    
    private $table;
    private $field_default;
    private $join_tables=[];
    private $query;
    private $conn_id = null;
    private $child_id = null;
    protected $glo=[];
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function get_all_tables()
    {
        global $all_data_tables;
        return $all_data_tables;
    }
    
    public function set($data)
    {
        return $this->db->insert($this->table, $data);
    }

    private function model_load_model($model_name)
    {
        //load file
        //$this->load->model($model_name);

        //all model in one model file
        $mod = new $model_name();
        return $mod;
    }
    
    public function update($save=false)
    {
        $is_update = false;
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach($props as $prop)
        {
            $pub_nm = $prop->getName();
            $pub_pro = $this->$pub_nm; // ex: $this->catagory
            if (is_object($pub_pro))
            {
                $conn_field = $pub_nm."_id"; //ex: catagory->catagory_id
                $this->db->set($conn_field, $pub_pro->id);
            }else if ($pub_nm == "id" && ($pub_pro != null or !$save))
            {
                $this->db->where('id',$pub_pro);
                $is_update = true;
            }else{
                $this->db->set($pub_nm, $pub_pro);
            }
        }
        if ($is_update)
        {
            $this->db->update($this->table);
        }else
        {
            $this->db->insert($this->table);
        }
    }
    
    public function save()
    {
        $this->update(true);
    }
    
    public function delete()
    {
        //delete all children <=> find all tables with join_tables key=$this->table & connect_field = $this->id
        $all_tables = $this->get_all_tables();
        foreach($all_tables as $table)
        {
            $new_model_nm = $table."_model";
            $new_model = $this->model_load_model($new_model_nm);
            foreach($new_model->get_join_tables() as $key=>$value)
            {
                //key = catagory_id, value = catagory
                if ($value == $this->table)
                {
                    $rows = $new_model->filter(array($key=>$this->id))->all();
                    foreach($rows as $row)
                    {
                        //recursive connect delete
                        $row->delete();
                    }
                    //recursive delete records of connect table
                    if (sizeof($rows) > 0)
                    {
                        $new_model->delete_rows(array($key=>$this->id));
                    }
                }
            }
        }
        $this->delete_rows(array("id"=>$this->id));
    }
    
    public function delete_rows($array_values)
    {
        foreach ($array_values as $key=>$value)
        {
            $this->db->where($key, $value);
            $this->db->delete($this->table);
            
        }
    }

    public function set_table($value)
    {
        $this->table =$value;
    }

    public function set_field_default($value)
    {
        $this->field_default =$value;
    }

    public function set_join_tables($table_nm, $conn_field)
    {
        $this->join_tables[$conn_field] =$table_nm;
    }

    public function get_join_tables()
    {
        return $this->join_tables;
    }

    private function set_conn_id($value)
    {
        $this->conn_id = $value;
    }

    public function get_conn_id()
    {
        return $this->conn_id;
    }

    private function set_child_id($value)
    {
        $this->child_id = $value;
    }

    public function get_child_id()
    {
        return $this->child_id;
    }

    public function limit($start, $end= NULL)
    {
        if ($end ==  NULL)
        {
            $this->db->limit($start);
        }else{
            $this->db->limit($start,$end);
        }
    }

    public function order_by($array_values)
    {
        foreach($array_values as $key=>$value)
        {
            $this->db->order_by($key,$value);
        }
        return $this;
    }

    public function group_by($value)
    {
        $this->db->group_by($value);
        return $this;
    }

    public function count_all_results()
    {
        return $this->db->count_all_results($this->table);
    }

    public function extra_select($array_values)
    {
        $select_str="*";
        foreach($array_values as $key=>$value)
        {
            $select_str .=",".$key." as ".$value;
        }
        $this->db->select($select_str);
        return $this;
    }

    public function value()
    {
        if (!isset($this->field_default))
        {
            $this->field_default="id";
        }
        return $this->{$this->field_default};
    }

    private function filter_in($key, $array_values, $and=true)
    {
        if(is_array($array_values) && sizeof($array_values)>0)
        {
            //in case of : array("id"=>[3,4])
            if ($and)
            {
                $this->query = $this->db->where_in($key, $array_values);
            }else
            {
                $this->query = $this->db->or_where_in($key, $array_values);
            }
        }else if (is_string($array_values))
        {
            //in case of: array("name"=>"abc")
            if ($and)
            {
                $this->query = $this->db->where($key, $array_values);
            }else
            {
                $this->query = $this->db->or_where($key, $array_values);
            }
        }else
        {
            //in case of: array("name"=>"") or array("name"=>null)
            if ($and)
            {
                $this->query = $this->db->where($key, null);
            }else
            {
                $this->query = $this->db->or_where($key, null);
            }
        }
        return $this;
    }

    private function filter_like($key, $array_values, $advance_search, $and=true)
    {
        $cond = "";
        if ($advance_search =="icontain" || $advance_search =="notIcontain" )
        {
            $cond = "both";
        }else if ($advance_search =="beginWith" || $advance_search =="notBeginWith" )
        {
            $cond = "after";
        }else if ($advance_search =="endWith" || $advance_search =="notEndWith" )
        {
            $cond = "before";
        }

        if (empty($cond))
        {
            return $this;
        }

        $pos = strpos($advance_search, "not");
        $like = false;
        if ($pos ===false)
        {
            $like = true;
        }

        if (is_string($array_values))
        {
            if ($like && $and)
            {
                $this->db->like($key, $array_values, $cond);
            }else if (!$like && $and)
            {
                $this->db->not_like($key, $array_values, $cond);
            }else if ($like && !$and)
            {
                $this->db->or_like($key, $array_values, $cond);
            }else if (!$like && !$and)
            {
                $this->db->or_not_like($key, $array_values, $cond);
            }
        }
        return $this;
    }

    public function connect_set($connect_table)
    {
        $new_model = $connect_table."_model";
        $filter_value = null;
        if (!is_null($this->child_id))
        {
            $filter_value = $this->child_id;
        }

        $new_model = $this->model_load_model($new_model);
        $condition =[];
        foreach ($new_model->get_join_tables() as $key=>$value)
        {
            if ($value == $this->table)
            {
                $condition[$key] = $filter_value;
            }
        }
        if (sizeof($condition)== 1)
        {
            $result = $this->model_load_model($new_model)->filter($condition);
        }else
        {
            $result = $this->model_load_model($new_model)->filter_or($condition);
        }
        return $result;
    }

    public function get($array_value = FALSE)
    {
        if ($array_value = FALSE)
        {
            return $this;
        }
        $this->filter($array_value);
        return $this;
    }

    public function filter($array_values, $and=true)
    {
        $array_seach = [];
        $count_input =0;
        if (sizeof($array_values) > 0)
        {
            if (!$and & sizeof($array_values) == 1)
            {
                show_error("Filter_or need atlease 2 arguments, 1 is given!");
            }

            foreach($array_values as $key=>$value)
            {
                $keys = explode("__", $key);
                if (sizeof($keys) > 1)
                {
                    $n = sizeof($keys);
                    $obj_in = null;
                    $search_advance = $keys[$n-1];
                    $like = false;
                    if ($search_advance == "icontain" || $search_advance == "notIcontain" ||
                    $search_advance == "beginWith" || $search_advance == "notBeginWith"||
                    $search_advance == "endWith" || $search_advance == "notEndWith")
                    {
                        $like = true;
                        $n = $n -1;
                    }

                    if (sizeof($keys)==2 && $like)
                    {
                        if($count_input == 0)
                        {
                            $obj_in = $this->filter_like($key[$n-1], $value, $search_advance);
                        }else
                        {
                            $obj_in = $this->filter_like($key[$n-1], $value, $search_advance, $and);
                        }
                    }

                    $loadModel = false;
                    while($n>1)
                    {
                        $conn_obj_filter = $this;
                        $model_in_nm = $this->table;
                        for ($i=0; $i<=$n-2;$i++)
                        {
                            $conn_obj_filter = $conn_obj_filter->$key[$i];
                            $model_in_nm = $conn_obj_filter->table;
                        }
                        $model_in = $this->model_load_model($model_in_nm."_model");
                        $loadModel = true;   
                        if ($n==sizeof($keys) || ($n==(sizeof($keys)-1) && $like) )
                        {
                            if (!$like)
                            {
                                $obj_in = $model_in->filter(array($keys[$n-1]=>$value));
                            }else
                            {
                                $obj_in = $model_in->filter_like($keys[$n-1], $value, $search_advance);
                            }
                        }else
                        {
                            $values_in = $obj_in->all();
                            $vals = [];
                            foreach($values_in as $value_in)
                            {
                                $vals[] = $value_in->id;
                            }
                            $conn_obj_filter = $conn_obj_filter->$key[$n-1];
                            $model_in_nm =$conn_obj_filter->table;
                            $obj_in=$model_in->filter_in($model_in_nm."_id".$vals);
                        }
                        $n = $n-1;
                    }

                        
                    if ($loadModel)
                    {
                        $values_in = $obj_in->all();
                        $vals = [];
                        foreach($values_in as $value_in)
                        {
                            $vals[] = $value_in->id;
                        }

                        $conn_obj_filter = $this;
                        $model_in_nm = $this->table;
                        for($i=0;$i<=$n-1;$i++)
                        {
                            $conn_obj_filter = $conn_obj_filter->$key[$i];
                            $model_in_nm = $conn_obj_filter->table;
                        }
                        $conn_table = $model_in_nm;
                        if ($count_input == 0)
                        {
                            $this->filter_in($conn_table."_id", $vals);
                        }else
                        {
                            $this->filter_in($conn_table."_id", $vals, $and);
                        }
                    }
                }else
                {
                    if ($count_input==0)
                    {
                        $this->filter_in($key, $value, true);
                    }else
                    {
                        $this->filter_in($key, $value, $and);
                    }
                }

                $count_input = $count_input+1;  
            }
        }
        return $this;
    }

    public function filter_or($array_values)
    {
        return $this->filter($array_values, false);
    }

    public function all()
    {
        $this->query = $this->db->get($this->table);
        $results = $this->query->result_array();
        return $this->forein_keys_to_objects($results);
    }

    public function one()
    {
        $this->query = $this->db->get($this->table);
        $results = $this->query->row_array();
        return $this->forein_key_to_object($results);
    }
    
    private function forein_key_to_object($result)
    {
        if (is_null($result))
        {
            return $result;
        }
        foreach ($result as $key=>$value)
        {
            if (array_key_exists($key, $this->join_tables))
            {
                unset($result[$key]);
                $join_table = $this->join_tables[$key];
                $new_model = $join_table."_model";
                $connect_name = preg_replace('/_id$/','', $key);
                $conn_obj = $this->model_load_model($new_model);
                $conn_obj = $conn_obj->filter(array("id"=>$value));
                $res = $conn_obj->one();
                $val_join=null;
                if (!is_null($res))
                {
                    $val_join = $res->id;
                }

                $conn_obj->set_conn_id($val_join);
                $conn_obj->set_child_id($result["id"]);
                $this->set_conn_id(($val_join));
                $this->set_child_id($result["id"]);
                $this->$connect_name= $conn_obj;
            }else
            {
                $this->$key = $value;
            }
        }
        return $this;
    }

    private function forein_keys_to_objects($results)
    {
        if (is_null($results))
        {
            return $results;
        }
        $obj_results=[];
        foreach ($results as $key1=>$result)
        {
            $object_result = new $this;
            foreach ($result as $key=>$value)
            {
                if (array_key_exists($key, $this->join_tables))
                {
                    unset($results[$key1][$key]);
                    $join_table = $this->join_tables[$key];
                    $new_model = $join_table."_model";
                    $connect_name = preg_replace('/_id$/','', $key);
                    $conn_obj = $this->model_load_model($new_model);
                    $conn_obj = $conn_obj->filter(array("id"=>$value));
                    $res = $conn_obj->one();
                    $val_join=null;
                    if (!is_null($res))
                    {
                        $val_join = $res->id;
                    }

                    $conn_obj->set_conn_id($val_join);
                    $conn_obj->set_child_id($result["id"]);
                    $object_result->set_conn_id(($val_join));
                    $object_result->set_child_id($result["id"]);
                    $object_result->$connect_name= $conn_obj;
                }else
                {
                    $object_result->$key = $value;
                }
            }
            $obj_results[] = $object_result;
        }
        return $obj_results;
    }

}
