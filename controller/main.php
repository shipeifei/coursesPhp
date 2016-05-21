<?php
class main extends spController
{
	function index(){
		$arr = array ('a'=>'弹道导弹','b'=>2,'c'=>3,'d'=>4,'e'=>5);
                 exit(json_encode($arr));
	}
        function time(){
         echo date("Y-m-d H:i:s");
        }
}