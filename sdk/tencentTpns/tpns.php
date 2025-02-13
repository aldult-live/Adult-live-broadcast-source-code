<?php
namespace tpns {
    const GUANGZHOU = "api.tpns.tencent.com";
    const SINGAPORE = "api.tpns.sgp.tencent.com";
    const HONGKONG  = "api.tpns.hk.tencent.com";
    const SHANGHAI  = "api.tpns.sh.tencent.com";

    //audience type
    const AUDIENCE_ALL               = "all";
    const AUDIENCE_TAG               = "tag";
    const AUDIENCE_TOKEN             = "token";
    const AUDIENCE_TOKEN_LIST        = "token_list";
    const AUDIENCE_ACCOUNT           = "account";
    const AUDIENCE_ACCOUNT_LIST      = "account_list";
    const AUDIENCE_ACCOUNT_PACKAGE   = "package_account_push";
    const AUDIENCE_TOKEN_PACKAGE     = "package_token_push";

    // tag operation type
    const TAG_OPERATOR_AND = "AND";
    const TAG_OPERATOR_OR  = "OR";

    //deprecated
    // platform type
    const PLATFORM_ANDROID  = "android";
    const PLATFORM_IOS      = "ios";

    // message type
    const MESSAGE_NOTIFY  = "notify";
    const MESSAGE_MESSAGE = "message";

    // environment type
    const ENVIRONMENT_PROD = "product";
    const ENVIRONMENT_DEV  = "dev";

    class AndroidActionActivityAttr {
        public $if = 0;
        public $pf = 0;

        public function filter() {
        }
    }
    class AndroidActionBrowserAttr {
        public $url = "";
        public $confirm = 0;

        public function filter() {
        }
    }

    class AndroidAction {
        public $action_type  = 1;
        public $activity     = "";
        public $aty_attr;                 //AndroidActionActivityAttr
        public $browser;                  //AndroidActionBrowserAttr
        public $intent       = "";

        public function filter() {
            if (isset($this->aty_attr) && $this->aty_attr != null) {
                if (method_exists($this->aty_attr, 'filter')) {
                    $this->aty_attr->filter();
                }
            } else {
                unset($this->aty_attr);
            }

            if (isset($this->browser) && $this->browser != null) {
                if (method_exists($this->browser, 'filter')) {
                    $this->browser->filter();
                }
            } else {
                unset($this->browser);
            }
        }
    }

    class AndroidMessage {
        public $n_ch_id         = "";
        public $n_ch_name       = "";
        public $xm_ch_id        = "";
        public $hw_ch_id        = "";
        public $oppo_ch_id      = "";
        public $vivo_ch_id      = "";
        public $builder_id      = 0;
        public $badge_type      = -1;
        public $ring            = 1;
        public $ring_raw        = "";
        public $vibrate         = 1;
        public $lights          = 1;
        public $clearable       = 1;
        public $icon_type       = 0;
        public $icon_res        = "";
        public $style_id        = 0;
        public $small_icon      = "";
        public $action;                     //AndroidAction
        public $custom_content  = "";     
        public $show_type       = 2;
        public $icon_color      = 0;

        public function filter() {
            if (isset($this->action) && $this->action != null) {
				
				
                if (method_exists($this->action, 'filter')) {
                    $this->action->filter();
                }
            } else {
                unset($this->action);
            }
        }
    }

    class Aps {
        public $alert;
        public $badge_type          = 0;
        public $category            = "";
        public $content_available   = 0;    //json to content-available
        public $sound               = "";
        public $mutable_content     = 1;    //json to mutable-content

        public function filter() {
            if (isset($this->alert) && $this->alert == null) {
                unset($this->alert);
            }
        }
    }

    class iOSMessage {
        public $aps;                 //Aps
        public $custom = "";

        public function filter () {
            if (isset($this->aps) && $this->aps != null) {
                if (method_exists($this->aps, 'filter')) {
                    $this->aps->filter();
                }
            } else {
                unset($this->aps);
            }
        }
    }

    class TagItem {
        public $tags;          //array of string
        public $is_not         = false;
        public $tags_operator  = "";
        public $items_operator = "";
        public $tag_type       = "";

        public function filter () {
            if (isset($this->tags) && $this->tags == null) {
                unset($this->tags);
            }
        }
    }

    class TagRule {
        public $tag_items;   //array of TagItem
        public $is_not       = false;
        public $operator     = "";

        public function filter () {
            if (isset($this->tag_items) && $this->tag_items == null) {
                unset($this->tag_items);
            }
        }
    }

    class AcceptTime {
        public $hour = "";
        public $min  = "";

        public function filter() {
        }
    }

    class AcceptTimeRange {
        public $start;      //AcceptTime
        public $end;        //AcceptTime

        public function filter () {
            if (isset($this->start) && $this->start != null) {
                if (method_exists($this->start, 'filter')) {
                    $this->start->filter();
                }
            } else {
                unset($this->start);
            }

            if (isset($this->end) && $this->end != null) {
                if (method_exists($this->end, 'filter')) {
                    $this->end->filter();
                }
            } else {
                unset($this->end);
            }
        }
    }

    class Message {
        public $title                    = ""; 
        public $content                  = "";
        public $accept_time;             //array of AcceptTimeRange
        public $thread_id                = "";
        public $thread_sumtext           = "";
        public $xg_media_resources       = "";
        public $xg_media_audio_resources = "";
        public $android;                 //AndroidMessage
        public $ios;                     //iOSMessage

        public function filter() {
            if (isset($this->accept_time) && $this->accept_time == null) {
                unset($this->accept_time);
            }

            if (isset($this->android) && $this->android != null) {
                if (method_exists($this->android, 'filter')){
                    $this->android->filter();
                }
            } else {
                unset($this->android);
            }

            if (isset($this->ios) && $this->ios != null) {
                if (method_exists($this->ios, 'filter')) {
                    $this->ios->filter();
                }
            } else {
                unset($this->ios);
            }
        }
    }

    class ChannelRule {
        public $channel = "";
        public $disable = false;

        public function filter() {
        }
    }

    class LoopParam {
        public $startDate  = "";
        public $endDate    = "";
        public $loopType   = 0;
        public $loopDayIndexs;  //array of uint32
        public $DayTimes;       //array of string

        public function filter() {
            if (isset($this->loopDayIndexs) && $this->loopDayIndexs == null) {
                unset($this->loopDayIndexs);
            } 

            if (isset($this->DayTimes) && $this->DayTimes == null) {
                unset($this->DayTimes);
            }
        }
    };


    class Request {
        public $audience_type = "";
        public $platform      = "";
        public $message;                // Message
        public $message_type  = "";

        public $tag_rules;              // array of TagRule
        public $token_list;             // array of string
        public $account_list;           // array of string

        public $environment           = "";
        public $upload_id             = 0;
        public $expire_time           = 259200;
        public $send_time             = "";
        public $multi_pkg             = false;
        public $plan_id               = "";
        public $account_push_type     = 0;
        public $account_type          = 0;
        public $collapse_id           = 0;
        public $push_speed            = 0; 
        public $tpns_online_push_type = 0;
        public $ignore_invalid_token  = 0;
        public $force_collapse        = false;

        public $channel_rules;         //array of ChannelRule
        public $loop_param;            //LoopParam

        public function filter() {
            if (isset($this->message) && $this->message != null) {
                if (method_exists($this->message, 'filter')){
                    $this->message->filter();
                }
            } else {
                unset($this->message);
            }

            if (isset($this->tag_rules) && $this->tag_rules == null) {
                unset($this->tag_rules);
            }
    
            if (isset($this->token_list) && $this->token_list == null) {
                unset($this->token_list);
            }

            if (isset($this->account_list) && $this->account_list == null) {
                unset($this->account_list);
            }

            if (isset($this->channel_rules) && $this->channel_rules == null) {
                unset($this->channel_rules);
            }

            if (isset($this->loop_param) && $this->loop_param != null) {
                if (method_exists($this->loop_param, 'filter')) {
                    $this->loop_param->filter();
                }
            } else {
                unset($this->loop_param);
            }
        }

        public function Validate() {
            if (empty($this->audience_type)) {
                throw new \Exception("audience_type is not set");
            }

            if ($this->audience_type != AUDIENCE_ALL && 
                $this->audience_type != AUDIENCE_TAG &&
                $this->audience_type != AUDIENCE_TOKEN &&
                $this->audience_type != AUDIENCE_TOKEN_LIST &&
                $this->audience_type != AUDIENCE_ACCOUNT &&
                $this->audience_type != AUDIENCE_ACCOUNT_LIST &&
                $this->audience_type != AUDIENCE_ACCOUNT_PACKAGE &&
                $this->audience_type != AUDIENCE_TOKEN_PACKAGE) {
                throw new \Exception ("invalid audience_type: ".$this->audience_type);
            }

            if ($this->audience_type == AUDIENCE_TOKEN || $this->audience_type == AUDIENCE_TOKEN_LIST) {
                if (empty($this->token_list)) {
                    throw new \Exception ("empty token_list");
                }
                if (!is_array($this->token_list)) {
                    throw new \Exception ("token_list need to be array");
                }
            }

            if ($this->audience_type == AUDIENCE_ACCOUNT || $this->audience_type == AUDIENCE_ACCOUNT_LIST) {
                if (empty($this->account_list)) {
                    throw new \Exception ("empty account_list");
                } 
                if (!is_array($this->account_list)) {
                    throw new \Exception ("account_list need to be array");
                }
            }

            if ($this->audience_type == AUDIENCE_TAG) {
                if (empty($this->tag_rules)) {
                    throw new \Exception ("empty tag_rules");
                }
                if (!is_array($this->tag_rules)) {
                    throw new \Exception ("tag_rules need to be array");
                }
            }

            //if (empty($this->platform)) {
            //    throw new \Exception("empty platform");
            //}

            //if ($this->platform != PLATFORM_ANDROID && $this->platform != PLATFORM_IOS) {
            //    throw new \Exception("invalid platform: " . $this->platform);
            //}

            if ($this->message == null) {
                throw new \Exception("empty message");
            }

            if (empty($this->message_type)) {
                throw new \Exception("empty message_type");
            }

            if ($this->message_type != MESSAGE_NOTIFY && $this->message_type != MESSAGE_MESSAGE) {
                throw new \Exception("invalid message_type: " . $this->message_type);
            }

            //if ($this->platform == PLATFORM_IOS) {
            if ($this->message->ios != null) {
                if (empty($this->environment)) {
                    throw new \Exception("empty environment");
                }
                if ($this->environment != ENVIRONMENT_PROD && $this->environment != ENVIRONMENT_DEV) {
                    throw new \Exception("invalid environment: " . $this->environment);
                }
            }

            if (isset($this->channel_rules)) {
                if (!is_array($this->channel_rules)) {
                    throw new \Exception ("channel_rules need to be array");
                }
            }
        }

        public function Marshal() {
            $this->filter();

            //if ($this->platform == PLATFORM_ANDROID) {
            //    unset($this->message->ios);
            //}

            //if ($this->platform == PLATFORM_IOS) {
            //    unset($this->message->android);

                if (isset($this->message) && $this->message != null) {
                    if (isset($this->message->ios) && $this->message->ios != null) {
                        if (isset($this->message->ios->aps) && $this->message->ios->aps != null) {
                            $aps = $this->message->ios->aps;
                            if (isset($aps->content_available) && $aps->content_available != null  &&
                                isset($aps->mutable_content) && $aps->mutable_content != null) {
                                $this->message->ios->aps = array(
                                    "alert"      => $aps->alert,
                                    "badge_type" => $aps->badge_type,
                                    "category" => $aps->category,
                                    "content-available" => $aps->content_available,
                                    "sound" => $aps->sound,
                                    "mutable-content" => $aps->mutable_content
                                );
                            }
                        }
                    }
                }
            //}

            $data = json_encode($this);
            return $data;
        }

    }

    // set audience type: AUDIENCE_ALL, AUDIENCE_TAG, AUDIENCE_TOKEN, AUDIENCE_TOKEN_LIST, AUDIENCE_ACCOUNT, AUDIENCE_ACCOUNT_LIST, AUDIENCE_ACCOUNT_PACKAGE, AUDIENCE_TOKEN_PACKAGE
    function WithAudienceType($type) {
        return function($r) use ($type) {
            $r->audience_type = $type;
        };
    }

    //deprecated
    // set platform: PLATFORM_ANDROID, PLATFORM_IOS
    function WithPlatform($platform) {
        return function($r) use ($platform) {
            $r->platform = $platform;
        };
    }

    //set message, type: Message
    function WithMessage($message) {
        return function($r) use ($message) {
            $r->message = $message;
        };
    }

    //set message title, type: string
    function WithTitle($title) {
        return function($r) use ($title) {
            if ($r->message == null) {
                $r->message = new Message;
            }
            $r->message->title = $title;
        };
    }

    //set message content, type: string
    function WithContent($content) {
        return function($r) use ($content) {
            if ($r->message == null) {
                $r->message = new Message;
            }
            $r->message->content = $content;
        };
    }

    //set message accept_time, type: array of AcceptTimeRange
    function WithAcceptTime($acceptTime) {
        return function($r) use ($acceptTime) {
            if ($r->message == null) {
                $r->message = new Message;
            }
            $r->message->accept_time = $acceptTime;
        };
    }

    //set message thread_id, type: string
    function WithThreadId($threadId) {
        return function($r) use ($threadId) {
            if ($r->message == null) {
                $r->message = new Message;
            }
            $r->message->thread_id = $threadId;
        };
    }

    //set message thread_sumtext, type: string
    function WithThreadSumText($threadSumText) {
        return function($r) use ($threadSumText) {
            if ($r->message == null) {
                $r->message = new Message;
            }
            $r->message->thread_sumtext = $threadSumText;
        };
    }

    //set message xg_media_resources, type string
    function WithXGMediaResources($xgMediaResources) {
        return function($r) use ($xgMediaResources) {
            if ($r->message == null) {
                $r->message = new Message;
            }
            $r->message->xg_media_resources = $xgMediaResources;
        };
    }

    //set message xg_media_audio_resources, type string
    function WithXGMediaAudioResources($xgMediaAudioResources) {
        return function($r) use ($xgMediaAudioResources) {
            if ($r->message == null) {
                $r->message = new Message;
            }
            $r->message->xg_media_audio_resources = $xgMediaAudioResources;
        };
    }

    //set message android, type AndroidMessage
    function WithAndroidMessage($android) {
        return function($r) use ($android) {
            if ($r->message == null) {
                $r->message = new Message;
            }
            $r->message->android = $android;
        };
    }

    //set message ios, type iOSMessage
    function WithIOSMessage($ios) {
        return function($r) use ($ios) {
            if ($r->message == null) {
                $r->message = new Message;
            }
            $r->message->ios = $ios;
        };
    }

    //set message_type, 'MESSAGE_NOTIFY' or 'MESSAGE_MESSAGE'
    function WithMessageType($type) {
        return function($r) use ($type) {
            $r->message_type = $type;
        };
    }

    //set tag_rules, type: array of TagRule
    function WithTagRules($tagRules) {
        return function($r) use ($tagRules) {
            $r->tag_rules = $tagRules;
        };
    }

    //set token_list, type: array of string
    function WithTokenList($tokenList) {
        return function($r) use ($tokenList) {
            $r->token_list = $tokenList;
        };
    }

    //set account_list, type: array of string
    function WithAccountList($accountList) {
        return function($r) use ($accountList) {
            $r->account_list = $accountList;
        };
    }

    //set environment, only for iOS, 'ENVIRONMENT_PROD' or 'ENVIRONMENT_DEV'
    function WithEnvironment($env) {
        return function($r) use ($env) {
            $r->environment = $env;
        };
    }

    //set upload_id, type: int
    function WithUploadId($uploadId) {
        return function($r) use ($uploadId) {
            $r->upload_id = $uploadId;
        };
    }

    //set expire_time, type: int
    function WithExpireTime($expireTime) {
        return function($r) use ($expireTime) {
            $r->expire_time = $expireTime;
        };
    }

    //set send_time, type: string
    function WithSendTime($sendtime) {
        return function($r) use ($sendtime) {
            $r->send_time = $sendtime;
        };
    }

    //set multi_pkg, type: boolean
    function WithMultiPkg($multiPkg) {
        return function($r) use ($multiPkg) {
            $r->multi_pkg = $multiPkg;
        };
    }

    //set plan_id, type: string
    function WithPlanId($planId) {
        return function($r) use ($planId) {
            $r->plan_id = $planId;
        };
    }

    //set account_push_type, type: int
    function WithAccountPushType($type) {
        return function($r) use ($type) {
            $r->account_push_type = $type;
        };
    }

    //set account_type, type: int
    function WithAccountType($type) {
        return function($r) use ($type) {
            $r->account_type = $type;
        };
    }

    //set collapse_id, type: int
    function WithCollapseId($collapseId) {
        return function($r) use ($collapseId) {
            $r->collapse_id = $collapseId;
        };
    }

    //set push_speed, type: int
    function WithPushSpeed($speed) {
        return function($r) use ($speed) {
            $r->push_speed = $speed;
        };
    }

    //set tpns_online_push_type, type int
    function WithTpnsOnlinePushType($type) {
        return function($r) use ($type) {
            $r->tpns_online_push_type = $type;
        };
    }

    //set ignore_invalid_token, type: bool
    function WithIgnoreInvalidToken($type) {
        return function($r) use ($type) {
            $r->ignore_invalid_token = $type ? 1 : 0;
        };
    }

    //set force_collapse, type: bool
    function WithForceCollapse($force) {
        return function($r) use ($force) {
            $r->force_collapse = $force;
        };
    }

    //set channel_rules, type: array of ChannelRule
    function WithChannelRules($channelRules) {
        return function($r) use ($channelRules) {
            $r->channel_rules = $channelRules;
        };
    }

    //set loop_param, type: LoopParam
    function WithLoopParam($param) {
        return function($r) use ($param) {
            $r->loop_param = $param;
        };
    }

    //@param: WithXXX
    //php<5.6 not support ...$args
    function NewRequest() {
        $r= new Request;
        $num = func_num_args();
        $args = func_get_args();
        for ($i = 0; $i < $num; $i++) {
            $args[$i]($r);
        }

        return $r;
    }


    class Stub {
        public $host;
        public $sign;

        public function __construct($accessId, $secretKey, $host) {
            $this->host = $host;
            if (strpos($host, "http://") === false && strpos($host, "https://") === false) {
                $this->host = "https://" . $host;
            }

            $this->sign = base64_encode($accessId . ":" . $secretKey);
        }

        public function Push($request) {
            $request->Validate();
            $data = $request->Marshal();

            $headers = array("Content-type: application/json;charset='utf-8'", "Authorization: Basic " . $this->sign);
            $url = $this->host . "/v3/push/app";

            $options = array(
                CURLOPT_HTTPHEADER      => $headers,
                CURLOPT_HEADER          => 0,
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_SSL_VERIFYHOST  => 0,
                CURLOPT_POST            => 1,
                CURLOPT_POSTFIELDS      => $data,
                CURLOPT_URL             => $url,
                CURLOPT_RETURNTRANSFER  => 1,
                CURLOPT_TIMEOUT         => 10000
            );

            $ch = curl_init();
            curl_setopt_array($ch, $options);
            $ret = curl_exec($ch);
            $error = curl_error($ch);
            $info = curl_getinfo($ch);

            curl_close($ch);

            if ($error != "") {
                throw new \Exception($error);
            }

            $code = $info["http_code"];

            /*var_dump($code);
            die;*/

            if ($code != 200) {
                //throw new \Exception("status: " . $code . ", message: " . $ret);
                return array('ret_code'=>$code,'err_msg'=>$ret);
            }

            /*array(8) {
              ["seq"]=>
              int(0)
              ["push_id"]=>
              string(9) "572728156"
              ["ret_code"]=>
              int(0)
              ["environment"]=>
              string(7) "product"
              ["err_msg"]=>
              string(8) "NO_ERROR"
              ["err_msg_zh"]=>
              string(0) ""
              ["result"]=>
              string(2) "{}"
              ["invalid_targe_list"]=>
              array(0) {
              }
            }*/

            return json_decode($ret, 1);
        }

        public function UploadFile($file) {
            $headers = array("Content-type: multipart/form-data", "Authorization: Basic " . $this->sign);

            $url = $this->host . "/v3/push/package/upload";

            $cfile = new \CURLFile($file,'multipart/form-data',basename($file));

            $options = array(
                CURLOPT_HTTPHEADER      => $headers,
                CURLOPT_HEADER          => 0,
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_SSL_VERIFYHOST  => 0,
                CURLOPT_POST            => 1,
                CURLOPT_URL             => $url,
                CURLOPT_RETURNTRANSFER  => 1,
                CURLOPT_TIMEOUT         => 10000,
                CURLOPT_POSTFIELDS      => array("file" => $cfile)
            );

            $ch = curl_init();
            curl_setopt_array($ch, $options);

            $ret = curl_exec($ch);
            $error = curl_error($ch);
            $info = curl_getinfo($ch);

            curl_close($ch);

            if ($error != "") {
                throw new \Exception($error);
            }

            $code = $info["http_code"];

            if ($code != 200) {
                throw new \Exception("status: " . $code . ", message: " . $ret);
            }

            $data = json_decode($ret, 1);

            if ($data["retCode"] != 0) {
                throw new \Exception("upload error, retCode: " . $data["retCode"] . ", errMsg: " . $data["errMsg"]);
            }
            return $data["uploadId"];

        }
    }
}
