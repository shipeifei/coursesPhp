<?php

class log extends spController {
    /*
     * 根据用户token获取用户编号
     * 
     */

    function getUserId() {
        $userToken = $this->spArgs('userToken');

        if (isset($userToken)) {
            // 构造查找条件
            $conditions = array('token' => $userToken);
            $user = spClass('cms_user');
            $result = $user->find($conditions);
            if ($result['id']) {//存在
                return $result['id'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /* 创建日志* */

    function create() {

        $newrow = array(// PHP的数组
            'modulename' => $this->spArgs('moduleName'),
            'moduleid' => 1,
            'operationdate' => date('Y-m-d H:i:s'),
            'detail' => $this->spArgs('detail'),
            'type' => $this->spArgs('type'),
            'userid' => $this->getUserId(),
            'usernmae' => 'admin'
        );
        $log = spClass('cms_log');
        $log->create($newrow);  // 进行新增操作
        exit(json_encode(array(
            'resultCode' => 1,
            'resultMessage' => '成功'
        )));
    }

    function delete() {

        // 构造查找条件
        $conditions = array('id' => $this->spArgs('id'));
        $log = spClass('cms_log');
        $log->delete($conditions);
        if (1 >= $log->affectedRows()) {
          

            //日志
            exit(json_encode(array(
                'resultCode' => 1,
                'resultMessage' => '成功'
            )));
        } else {
            exit(json_encode(array(
                'resultCode' => 0,
                'resultMessage' => '失败'
            )));
        }
    }

    /*
     * 日志列表 
     * * */

    function logList() {
        $log = spClass('cms_log');
      
        $conditions = " 1=1 ";
        if ($this->spArgs('userId') != 0) {
            $conditions = $conditions . ' and  userid=' . $this->spArgs('userId');
        }
        if (!empty($this->spArgs('startDate')) && !empty($this->spArgs('endDate'))) {
            $conditions = $conditions .  ' and operationdate>="' . $this->spArgs('startDate') . ' 00:00:00 "';
            $conditions = $conditions . ' and operationdate<="' . $this->spArgs('endDate') . ' 23:59:59"';
        } else if (!empty($this->spArgs('startDate'))) {
            $conditions = $conditions .  ' and operationdate>="' . $this->spArgs('startDate') . ' 00:00:00 "';
        }
          $count = $log->findCount($conditions);
        $this->results = $log->spLinker()->spPager($this->spArgs('currentPage', 1), $this->spArgs('pageSize', 20))->findAll($conditions, ' operationdate desc');
        if (count($this->results)>0) {
            $arr = array(
                'resultCode' => 1,
                'resultMessage' => '成功',
                'responseObject' => array(
                    'totalSize' => $count,
                    'log' => $this->results
                )
            );
        }
        else
        {
             $arr = array(
                'resultCode' => 1,
                'resultMessage' => '成功',
                'responseObject' => array(
                    'totalSize' => $count,
                    'log' =>null
                )
            );
        }
        exit(json_encode($arr));
    }

}
