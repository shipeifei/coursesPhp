<?php

class role extends spController
{
    function roleList()
    {
        $role=  spClass('cms_role');
        $result =$role->findAll(); 
         $arr = array(
            'resultCode' => 1,
            'resultMessage' => '成功',
            'responseObject' => array(
                'role' => $result
            )
        );
        exit(json_encode($arr));
    }
}

