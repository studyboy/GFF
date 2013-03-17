<?php
/**
 * 系统配置参数
 * @version 2013/03/06
 */
return $config['system'] =array(
   'db'=>array(
         'db_host'=>'',
         'db_database'=>'',
         'db_table_prefix'=>'',
         'db_password' =>'',
         'db_user'=>'',
         'db_charset'=>'utf8',
         'db_connect' =>''
      ),
   'route' =>array(
         'default_module'   =>'home',
         'default_controller' =>'index',   //
         'default_action' =>'index',       //
      ),
   'cache'=>array(
          'cache_dir'    =>'',                 //缓存目录
          'cache_prefix' =>'',                 //缓存前缀
          'cache_time'   =>'',                 //缓存时间
          'cache_mode'   =>''                  // 缓存模式
       )
);