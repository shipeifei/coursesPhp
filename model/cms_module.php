<?php
class cms_module extends spModel{
        var $table = "cms_module";
        var $pk = "id";
         // 由spModel的变量$linker来设置表间关联
        var $linker = array(
                array(
                        'type' => 'hasmany',   // 关联类型，这里是一对多关联
                        'map' => 'roles',    // 关联的标识
                        'mapkey' => 'id', // 本表与对应表关联的字段名
                        'fclass' => 'cms_module_role', // 对应表的类名
                        'fkey' => 'moduleid',    // 对应表中关联的字段名
                        'enabled' => true     // 启用关联
                )
        );
}

