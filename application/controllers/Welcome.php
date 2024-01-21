<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
        * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		// $this->load->view('welcome_message');

		$this->load->model('all_model');
        
        $obj_catagory = new Catagory_model();
        $cats = $obj_catagory->filter(array("catagory_id"=>null))->all();
        foreach ($cats as $cat)
        {
            echo "<br>".$cat->name;
        }

        //insert
        // echo "<br>insert car sub";
        // $obj_catagory = new Catagory_model();
        // $obj_catagory->name = "car sub 1";
        // $obj_catagory->save();
        // echo "<br>insert car:".$obj_catagory->id;



        //default value
        /* $cats = $obj_catagory->filter(array("name"=>"car"))->all();
        foreach($cats as $cat)
        {
            echo $cat->value().'<br>';
        } */

        //update idate
        // echo "<br>update catagory_id";
        // $obj_catagory = new Catagory_model();
        // $cat = $obj_catagory->filter(array("name"=>"car"))->one();
        // if ($cat != null){
        //     $cat->catagory_id = 2;
        //     $cat->update();
        // }

        //insert
        // $obj_catagory = new Catagory_model();
        // $obj_catagory->name = "my_car";
        // $obj_catagory->save();
        // echo "<br>insert my_car:".$obj_catagory->id;

        //update by object
        // $obj_catagory = new Catagory_model();
        // $cat = $obj_catagory->filter(array("name"=>"car"))->one();
        // if ($cat != null)
        // {
        //     $obj_catagory = new Catagory_model();
        //     $o = $obj_catagory->filter(array("name"=>"my_car"))->one();
        //     $cat->catagory = $o;
        //     $cat->update();
        //     // echo "<PRE>";print_r($cat);echo '</PRE>';
        // }

        // $obj_catagory = new Catagory_model();
        // $cat = $obj_catagory->filter(array("name"=>"car"))->one();
        // if ($cat != null)
        // {
        //     echo "<br>insert car sub";
        //     $obj_sub_catagory = new Catagory_model();
        //     $obj_sub_catagory->name = "car sub 2";
        //     $obj_sub_catagory->catagory = $cat;
        //     $obj_sub_catagory->save();
        //     echo "<br>insert car:".$obj_sub_catagory->id;
        // }

        // $obj_catagory = new Catagory_model();
        // $cat = $obj_catagory->filter(array("name" => "car"))->one();
        // if ($cat != null) {
            // $cats = $cat->connect_set("catagory")->all();
            // foreach ($cats as $cat) {
                // echo "<br>" . $cat->name . " - catagory_id:" . $cat->catagory_id;
            // }
        // }

        //update
        // $cat = $obj_catagory->filter(array("name"=>"car"))->one();
        // if ($cat != null)
        // {
        // $cat->name = "my_car";
        // $cat->update();
        // }

        //delete one
        // $cat = $obj_catagory->filter(array("name"=>"my_car"))->one();
        // if ($cat != null)
        // {
        // $cat->delete();
        // }

        //delete all
        // $cats = $obj_catagory->filter(array("name"=>"my_car"))->all();
        // if (count($cats) > 0)
        // {
        // foreach ($cats as $cat)
        // {
        // $cat->delete();
        // }
        // }

        // $num = $obj_catagory->filter(array("name"=>"car"))->count_all_results();
        // echo "count_all_results:".$num;
	}
}
