<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function mydate($time = null){
      return  $_REQUEST['apiKey'];
//        if( null == $time )$time = time(); // 默认是当前时间
//        if( $time > (time() - 3600) ){
//                return "刚才";
//        }elseif( $time > (time() - 3600 * 24) ){
//                return "今天";
//        }elseif( $time > (time() -  3600 * 24 * 2) ){
//                return "昨天";
//        }elseif( $time > (time() -  3600 * 24 * 3) ){
//                return "前天";
//        }else{
//                return date("Y-m-d H:s", $time);
//        }
}

function systemApi()
{
    
}