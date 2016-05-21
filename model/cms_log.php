<?php
class cms_log extends spModel{
        var $table = "cms_log";
        var $pk = "id";
         // 由spModel的变量$linker来设置表间关联
        var $linker = array(
                array(
                        'type' => 'hasone',   // 关联类型，这里是一对一关联
                        'map' => 'name',    // 关联的标识
                        'mapkey' => 'userid', // 本表与对应表关联的字段名
                        'fclass' => 'cms_user', // 对应表的类名
                        'fkey' => 'id',    // 对应表中关联的字段名
                        'enabled' => true     // 启用关联
                )
        );
}
