<?php	return array (

    'ios'=>array(
        'codingmode' => '2',  //编码 0自动，1软编，2硬编
        'resolution' => '4',  //分辨率-->3:360X640 ,4: 540X960 ,5:720X1280 分辨率修改时,码率也许对应调整
        'isauto' => '1',  //是否自适应 0否1是   
        'fps' => '20',  //帧数
        'fps_min' => '20',  //最低帧数
        'fps_max' => '30',  //最高帧数
        'gop' => '3',  //关键帧间隔 
        'bitrate' => '1800',  //初始码率  kbps
        'bitrate_min' => '1800',  //最低码率
        'bitrate_max' => '1800',  //最高码率    //最高分辨是 码率为:1000
        'audiorate' => '44100',  //音频采样率  Hz
        'audiobitrate' => '48',  //音频码率 kbps
        
        'preview_fps' => '15',  //预览帧数
        'preview_resolution' => '1',  //预览分辨率
    ),
    'android'=>array( //金山sdk使用，腾讯sdk安卓端固定写好的

        'codingmode' => '3',  //编码 1自动，3软编，2硬编
        'resolution' => '1',  //分辨率 
        'isauto' => '1',  //是否自适应 0否1是 
        'fps' => '20',  //帧数
        'fps_min' => '20',  //最低帧数
        'fps_max' => '30',  //最高帧数
        'gop' => '3',  //关键帧间隔 
        'bitrate' => '500',  //初始码率  kbps
        'bitrate_min' => '500',  //最低码率
        'bitrate_max' => '800',  //最高码率
        'audiorate' => '48',  //音频采样率  Hz
        //'audiobitrate' => '48',  //音频码率 kbps
        
        'preview_fps' => '15',  //预览帧数
        'preview_resolution' => '1',  //预览分辨率
    ),

    'android_tx'=>array( //腾讯sdk安卓端使用

        'codingmode' => '3',  //编码 1自动，3软编，2硬编
        'resolution' => '1',  //分辨率   
        'isauto' => '1',  //是否自适应 0否1是 
        'fps' => '15',  //帧数
        'fps_min' => '20',  //最低帧数
        'fps_max' => '30',  //最高帧数
        'gop' => '1',  //关键帧间隔 
        'bitrate' => '1200',  //初始码率  kbps //最高分辨是 码率为:2400
        'bitrate_min' => '800',  //最低码率  //最高分辨是 码率为:1600
        'bitrate_max' => '1500',  //最高码率   //最高分辨是 码率为:3000
        'audiorate' => '48000',  //音频采样率  Hz
        //'audiobitrate' => '48',  //音频码率 kbps
        
        'preview_fps' => '15',  //预览帧数
        'preview_resolution' => '1',  //预览分辨率
    ),
    
    /* IOS分辨率 -金山版本
        1 AVCaptureSessionPreset320x240
        2 AVCaptureSessionPreset352x288
        3 AVCaptureSessionPreset640x480    //腾讯版-  360_640
        4 AVCaptureSessionPreset960x540    //腾讯版-  540_960
        5 AVCaptureSessionPreset1280x720   //腾讯版-  720_1280
        6 AVCaptureSessionPreset1920x1080
        7 AVCaptureSessionPreset3840x2160
        8 AVCaptureSessionPresetiFrame960x540
        9 AVCaptureSessionPresetiFrame1280x720

       安卓分辨率 -金山版本
        0 VIDEO_RESOLUTION_360P  //腾讯版- 360_640
        1 VIDEO_RESOLUTION_480P  //腾讯版- 540_960
        2 VIDEO_RESOLUTION_540P  //腾讯版- 720_1280
        3 VIDEO_RESOLUTION_720P
        4 VIDEO_RESOLUTION_1080P
    */
   
   /*
   安卓分辨率 - 腾讯云
    0 => 360_640 ;
    1 => 540_960 ;
    2 => 720_1280 ;
    3 => 640_360 ;
    4 => 960_540 ;
    5 => 1280_720 ;
    6 => 320_480 ;
    7 => 180_320 ;
    8 => 270_480 ;
    9 => 320_180 ;
    10 => 480_270 ;
    11 => 240_320 ;
    12 => 360_480 ;
    13 => 480_640 ;
    14 => 320_240 ;
    15 => 480_360 ;
    16 => 640_480 ;
    17 => 480_480 ;
    18 => 270_270 ;
    19 => 160_160 ;
    30 => 1080_1920 ;
    31 => 1920_1080 ;
    */
);