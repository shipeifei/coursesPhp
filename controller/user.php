<?php

class user extends spController {
    /*
     * 检测用户token，判断是否超时
     * 
     */

    function checkUserToken() {
        $userToken = $this->spArgs('userToken', '');
        // 0:token正常使用 1：token超时 2：token不存在
        $status = 0;
        if (isset($userToken)) {
            // 构造查找条件
            $conditions = array('token' => $userToken);
            $user = spClass('cms_user');
            $result = $user->find($conditions);
            if ($result['token']) {//存在
                if (strtotime(date('y-m-d h:i:s')) >= strtotime($result['tokenendtime'])) {//token超时
                    $status = 1;
                }
            } else {
                $status = 2;
            }
        } else {
            $status = 2;
        }
        $arr = array(
            'resultCode' => 1,
            'resultMessage' => '成功',
            'responseObject' => array(
                'user' => array(
                    'status' => $status
                )
            )
        );
        exit(json_encode($arr));
    }

    /*
     * cms用户登录
     * * */

    function login() {

        $userName = $this->spArgs('userName');
        $userPassword = $this->spArgs('userPassword');

        //判断用户名和密码是否为空
        if (!empty($userName) && !empty($userPassword)) {
            // 构造查找条件
            $conditions = array('username' => $userName, 'userpassword' => md5($userPassword), 'status' => 1);
            $user = spClass('cms_user');
            $result = $user->find($conditions);

            if ($result['username']) {
                // 生成token
                $token = 'kejian' . substr(date('ymdHis'), 2, 8) . mt_rand(100000, 999999);
                // 构造查找条件
                $update_conditions = array('id' => $result['id']);
                $updatesql = array(
                    'token' => md5($token),
                    'lastlogintime' => date('Y-m-d H:i:s'),
                    'tokenendtime' => date('Y-m-d H:i:s', strtotime('+7 day')),
                    'logincount' => $result['logincount'] + 1
                );
                $result_update = $user->update($update_conditions, $updatesql);
                if ($result_update) {
                    $arr = array(
                        'resultCode' => 1,
                        'resultMessage' => '成功',
                        'responseObject' => array(
                            'user' => array(
                                'userName' => $result['username'],
                                'userToken' => md5($token)
                            )
                        )
                    );
                } else {
                    $arr = array(
                        'resultCode' => 0,
                        'resultMessage' => '登录失败,原因为获取用户token失败'
                    );
                }
            } else {
                $arr = array(
                    'resultCode' => 0,
                    'resultMessage' => '登录失败,用户名或者密码不存在或者用户被禁用'
                );
            }
            exit(json_encode($arr));
        }
    }

    /*
     * 检查用户是否已经存在
     * * */

    function checkExistUser() {
        // 构造查找条件
        $conditions = array('username' => $this->spArgs('userName'));
        $user = spClass('cms_user');
        $result = $user->findCount($conditions);
        $arr = array(
            'resultCode' => 1,
            'resultMessage' => '成功',
            'responseObject' => array(
                'result' => array(
                    'count' => $result
                )
            )
        );
        exit(json_encode($arr));
    }

    /*
     * 改变用户状态
     * * */

    function changeStatus() {
        $user = spClass('cms_user');
        // 构造查找条件
        $update_conditions = array('id' => $this->spArgs('id'));
        $updatesql = array(
            'status' => $this->spArgs('status')
        );
        $result_update = $user->update($update_conditions, $updatesql);
        if ($result_update) {
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

    /* 创建用户* */

    function create() {


        $roleId = $this->spArgs('roleID');
        $user = spClass('cms_user');
        $newrow = array(// PHP的数组
            'code' => substr(date('ymdHis'), 2, 8) . mt_rand(100000, 999999),
            'username' => $this->spArgs('userName'),
            'begindate' => date('Y-m-d H:i:s'),
            'createdate' => date('Y-m-d H:i:s'),
            'loginip' => $this->spArgs('loginIp'),
            'userpassword' => md5($this->spArgs('userPassword')),
            'rolecode' => $this->spArgs('roleCode'),
            'modulerolecode' => $this->spArgs('moduleRoleCode'),
            'lastlogintime' => date('Y-m-d H:i:s'),
            'realname' => $this->spArgs('userRealName')
        );
        $result = $user->create($newrow);  // 进行新增操作
        if ($result) {
            $this->createLog('用户管理', '创建', '创建用户');

            $code = 1;
            //用户权限列表
            if (!empty($roleId)) {
                $userRole = spClass('cms_user_role');
                $arrayRole = explode(',', $roleId);
                foreach ($arrayRole as $key => $value) {
                    $sqlParmes = array(
                        'userid' => $result,
                        'roleid' => $value
                    );
                    $userRole->create($sqlParmes);
                }
            }
        } else {
            $code = 0;
        }

        exit(json_encode(array(
            'resultCode' => $code,
            'resultMessage' => $code == 1 ? '成功' : '失败'
        )));
    }

    function update() {
        $conditions = array('id' => $this->spArgs('id'));
        $newrow = array(// PHP的数组

            'username' => $this->spArgs('userName'),
            'rolecode' => $this->spArgs('roleCode'),
            'modulerolecode' => $this->spArgs('moduleRoleCode'),
            'realname' => $this->spArgs('userRealName')
        );

        $user = spClass('cms_user');
        $result = $user->update($conditions, $newrow);  // 进行新增操作
        if ($result) {
            $this->createLog('用户管理', '修改', '修改用户');

            $code = 1;
        } else {
            $code = 0;
        }

        exit(json_encode(array(
            'resultCode' => $code,
            'resultMessage' => $code == 1 ? '成功' : '失败'
        )));
    }

    function delete() {

        // 构造查找条件
        $conditions = array('id' => $this->spArgs('id'));
        $user = spClass('cms_user');
        $user->delete($conditions);
        if (1 >= $user->affectedRows()) {
            //删除相对应权限
            $usrRole = spClass('cms_user_role');
            $RoleConditions = array('userid' => $this->spArgs('id'));
            $usrRole->delete($RoleConditions);

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
     * 用户列表 
     * * */

    function userList() {
        $user = spClass('cms_user');
        if (!empty($this->spArgs('userName'))) {
            $conditions = array('username' => $this->spArgs('userName'));
        }
        $count = $user->findCount();
        $this->results = $user->spPager($this->spArgs('currentPage', 1), $this->spArgs('pageSize', 20))->findAll($conditions, ' createdate desc');
        $arr = array(
            'resultCode' => 1,
            'resultMessage' => '成功',
            'responseObject' => array(
                'totalSize' => $count,
                'user' => $this->results
            )
        );
        exit(json_encode($arr));
    }

    /*
     * 用户列表 ,无分页
     * * */

    function userListNoPage() {
        $user = spClass('cms_user');
        $results = $user->findAll(null, ' createdate desc');
        $arr = array(
            'resultCode' => 1,
            'resultMessage' => '成功',
            'responseObject' => array(
                'user' => $results
            )
        );
        exit(json_encode($arr));
    }

    /*
     * 通过编号获取用户信息
     * ** */

    function getUserById() {
        $user = spClass('cms_user');
        $conditions = array('id' => $this->spArgs('userId'));
        $this->results = $user->find($conditions);
        $arr = array(
            'resultCode' => 1,
            'resultMessage' => '成功',
            'responseObject' => array(
                'user' => $this->results
            )
        );
        exit(json_encode($arr));
    }

}
