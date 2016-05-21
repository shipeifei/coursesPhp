<?php

class module extends spController {
    /*
     * 获取最大的排序好号
     * 
     */

    function getMaxOrder() {

        $module = spClass('cms_module');
        $result = $module->findCount(null);
        if ($result > 0) {//存在
            exit(json_encode(array(
                'resultCode' => 1,
                'resultMessage' => '成功',
                'responseObject' => array(
                    'order' => $result + 1
                )
            )));
        } else {
            exit(json_encode(array(
                'resultCode' => 1,
                'resultMessage' => '成功',
                'responseObject' => array(
                    'order' => 1
                )
            )));
        }
    }

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

    function update() {
        $moduleModel = json_decode($this->spArgs('module'));
        $conditions = array('id' => $moduleModel->{'id'});
        $newrow = array(// PHP的数组
            'modulename' => $moduleModel->{'modulename'},
            'modulecode' => $moduleModel->{'modulecode'},
            'moduleurl' => $moduleModel->{'moduleurl'},
            'createdate' => date('Y-m-d H:i:s'),
            'moduleorder' => $moduleModel->{'moduleorder'},
            'moduleicon' => $moduleModel->{'moduleicon'},
            'comments' => $moduleModel->{'comments'}
        );
        $module = spClass('cms_module');
        $result = $module->update($conditions, $newrow);  // 进行操作
        if ($result) {
            $this->createLog('模块管理', '更新', '更新');
            $code = 1;
        } else {
            $code = 0;
        }

        exit(json_encode(array(
            'resultCode' => $code,
            'resultMessage' => $code == 1 ? '成功' : '失败'
        )));
    }

    /*
     * 更新排序
     * * */

    function updateOrder() {

        
        $splitResult = explode(',', $this->spArgs('ids'));

        while (list($key, $val) = each($splitResult)) {
            $conditions = array('id' => $val);
            $newrow = array(// PHP的数组
                'moduleorder' => $key + 1
            );
            $module = spClass('cms_module');
            $result = $module->update($conditions, $newrow);  // 进行操作
        }

        if ($result) {
            $this->createLog('模块管理', '更新排序', '更新排序');
            $code = 1;
        } else {
            $code = 0;
        }

        exit(json_encode(array(
            'resultCode' => $code,
            'resultMessage' => $code == 1 ? '成功' : '失败'
        )));
    }

    /* 创建* */

    function create() {
        $moduleModel = json_decode($this->spArgs('module'));

        $newrow = array(// PHP的数组
            'modulename' => $moduleModel->{'modulename'},
            'modulecode' => $moduleModel->{'modulecode'},
            'moduleurl' => $moduleModel->{'moduleurl'},
            'createdate' => date('Y-m-d H:i:s'),
            'moduleorder' => $moduleModel->{'moduleorder'},
            'modulestatus' => 1,
            'moduleparentid' => $moduleModel->{'moduleparentid'},
            'modulelevel' => $moduleModel->{'modulelevel'},
            'moduleicon' => $moduleModel->{'moduleicon'},
            'comments' => $moduleModel->{'comments'}
        );
        $module = spClass('cms_module');
        $module->create($newrow);  // 进行新增操作
        exit(json_encode(array(
            'resultCode' => 1,
            'resultMessage' => '成功'
        )));
    }

    function delete() {
        $module = spClass('cms_module');
        // 判断删除的模块是否包含子模块
        $subconditions = array('moduleparentid' => $this->spArgs('id'));
        $count = $module->findCount($subconditions);
        if ($count > 0) {
            exit(json_encode(array(
                'resultCode' => 2,
                'resultMessage' => '请先删除该模块下的子模块'
            )));
        }
        $conditions = array('id' => $this->spArgs('id'));
        $module->delete($conditions);
        if (1 >= $module->affectedRows()) {
             $this->createLog('模块管理', '删除', '删除');
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
     * 改变状态
     * * */

    function changeStatus() {
        $module = spClass('cms_module');
        // 构造查找条件
        $update_conditions = array('id' => $this->spArgs('id'));
        $updatesql = array(
            'modulestatus' => $this->spArgs('status')
        );
        $result_update = $module->update($update_conditions, $updatesql);
        if ($result_update) {
             $this->createLog('模块管理', '改变', '改变状态');
            $arr = array(
                'resultCode' => 1,
                'resultMessage' => '成功'
            );
        } else {
            $arr = array(
                'resultCode' => 0,
                'resultMessage' => '更新失败'
            );
        }
        exit(json_encode($arr));
    }

    /*
     * 列表 
     * * */

    function moduleList() {
        $module = spClass('cms_module');
        $conditions = array('moduleparentid' => $this->spArgs('parentId'));
        $count = $module->findCount($conditions);
        $this->results = $module->spLinker()->spPager($this->spArgs('currentPage', 1), $this->spArgs('pageSize', 20))->findAll($conditions, ' moduleorder asc');
        if (count($this->results) > 0) {
            $arr = array(
                'resultCode' => 1,
                'resultMessage' => '成功',
                'responseObject' => array(
                    'totalSize' => $count,
                    'module' => $this->results
                )
            );
        } else {
            $arr = array(
                'resultCode' => 1,
                'resultMessage' => '成功',
                'responseObject' => array(
                    'totalSize' => $count,
                    'module' => null
                )
            );
        }
        exit(json_encode($arr));
    }

    /*
     * 列表 
     * * */

    function getModuleByParentId() {
        $module = spClass('cms_module');
        $conditions = array('moduleparentid' => $this->spArgs('parentId'));

        $this->results = $module->findAll($conditions, ' moduleorder asc');
        if (count($this->results) > 0) {
            $arr = array(
                'resultCode' => 1,
                'resultMessage' => '成功',
                'responseObject' => array(
                    'module' => $this->results
                )
            );
        } else {
            $arr = array(
                'resultCode' => 1,
                'resultMessage' => '成功',
                'responseObject' => array(
                    'module' => null
                )
            );
        }
        exit(json_encode($arr));
    }

    function getSingleModuleById() {
        $module = spClass('cms_module');
        $conditions = array('id' => $this->spArgs('id'));
        $this->results = $module->find($conditions);
        $arr = array(
            'resultCode' => 1,
            'resultMessage' => '成功',
            'responseObject' => array(
                'module' => $this->results
            )
        );
        exit(json_encode($arr));
    }

}
