<?php
/**
 * 请在下面放置任何您需要的应用配置
 */

return array(

    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => array(
        //'sign' => array('name' => 'sign', 'require' => true),
    ),
    'REDIS_HOST' => "127.0.0.1",
    'REDIS_AUTH' => "rPwYbx5",
    'REDIS_PORT' => "6379",
    
    'sign_key' => '76576076c1f5f657b634e966c8836a06',
		
	'uptype'=>1,//上传方式：1表示 七牛，2表示 本地，3表示亚马逊 
		/**
     * 七牛相关配置
     */
    'Qiniu' =>  array(
        //ak
        'accessKey' => 'k3iJ_1o2W2G66JwxI0Inoi6lJnKca2DTf1GHCyGS',
        //sk
        'secretKey' => 'M6cg09Hm7D9JoKkrnBdbjTGF2ubZ2R4BIjMY20U3',
        //存储空间
        'space_bucket' => 'pangpanglive',
        //cdn加速域名 格式：http(s)://a.com，结尾不带/
        'space_host' => 'https://qiniu.bjyjzx.net',  
        //区域上传域名(服务端)  参考文档：https://developer.qiniu.com/kodo/manual/1671/region-endpoint
        'uphost' => 'https://up.qiniup.com', 
        //七牛云存储区域 华东：z0，华北：z1，华南：z2，北美：na0，东南亚：as0，参考文档：https://developer.qiniu.com/kodo/manual/1671/
        'region'=>'z0',
    ),

    
		
		 /**
     * 本地上传
     */
    'UCloudEngine' => 'local',

    /**
     * 本地存储相关配置（UCloudEngine为local时的配置）
     */
    'UCloud' => array(
        //对应的文件路径  站点域名
        'host' => 'https://x.com/upload' 
    ),
		
		/**
     * 云上传引擎,支持local,oss,upyun
     */
    //'UCloudEngine' => 'oss',

    /**
     * 云上传对应引擎相关配置
     * 如果UCloudEngine不为local,则需要按以下配置
     */
   /*  'UCloud' => array(
        //上传的API地址,不带http://,以下api为阿里云OSS杭州节点
        'api' => 'oss-cn-hangzhou.aliyuncs.com',

        //统一的key
        'accessKey' => '',
        'secretKey' => '',

        //自定义配置的空间
        'bucket' => '',
        'host' => 'http://image.xxx.com', //必带http:// 末尾不带/

        'timeout' => 90
    ), */
		

		
);
