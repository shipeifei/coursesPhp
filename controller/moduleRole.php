<?php

class moduleRole extends spController {

    
    /***
     * 更新权限
     */
    function update() {
        $moduleModel = json_decode($this->spArgs('moduleRole'));
        $newrow = array(// PHP的数组
            'moduleid' => $moduleModel->{'moduleid'},
            'rolecode' => $moduleModel->{'rolecode'},
            'rolename' => $moduleModel->{'rolename'},
            'createdate' => date('Y-m-d H:i:s')
        );
        $module = spClass('cms_module_role');
        $conditions = array('id' => $moduleModel->{'id'});

        $result = $module->update($conditions, $newrow);  // 进行操作
        if ($result) {
            $this->createLog('模块权限管理', '更新', '更新');
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
        $moduleModel = json_decode($this->spArgs('moduleRole'));
        $newrow = array(// PHP的数组
            'moduleid' => $moduleModel->{'moduleid'},
            'rolecode' => $moduleModel->{'rolecode'},
            'rolename' => $moduleModel->{'rolename'},
            'createdate' => date('Y-m-d H:i:s')
        );
        $module = spClass('cms_module_role');
        $resultId = $module->create($newrow);  // 进行新增操作

        if ($resultId > 0) {
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

    function delete() {
        $module = spClass('cms_module_role');
        $conditions = array('id' => $this->spArgs('id'));
        $module->delete($conditions);
        if (1 >= $module->affectedRows()) {
            $this->createLog('模块权限管理', '删除', '删除');
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
