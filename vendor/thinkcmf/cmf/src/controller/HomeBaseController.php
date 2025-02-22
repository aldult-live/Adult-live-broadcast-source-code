<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-present http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace cmf\controller;

use think\facade\Db;
use app\admin\model\ThemeModel;
use think\facade\View;

class HomeBaseController extends BaseController
{

    protected function initialize()
    {
        // 监听home_init
        hook('home_init');
        parent::initialize();
        //redis缓存开启
        connectionRedis();

        $siteInfo = getConfigPub();

        if(isset($siteInfo['sina_icon'])){
            $siteInfo['sina_icon']=get_upload_path($siteInfo['sina_icon']);
        }
        if(isset($siteInfo['qq_icon'])){
            $siteInfo['qq_icon']=get_upload_path($siteInfo['qq_icon']);
        }
        if(isset($siteInfo['apk_ewm'])){
            $siteInfo['apk_ewm']=get_upload_path($siteInfo['apk_ewm']);
        }
        if(isset($siteInfo['ipa_ewm'])){
            $siteInfo['ipa_ewm']=get_upload_path($siteInfo['ipa_ewm']);
        }
        if(isset($siteInfo['wechat_ewm'])){
            $siteInfo['wechat_ewm']=get_upload_path($siteInfo['wechat_ewm']);
        }
        if(isset($siteInfo['qr_url'])){
            $siteInfo['qr_url']=get_upload_path($siteInfo['qr_url']);
        }else{
            $siteInfo['qr_url']='';
        }

        View::share('site_info', $siteInfo);

        $configpri=getConfigPri();
        $this->configpub=$siteInfo;
        $this->configpri=$configpri;
        $this->assign("configpub",$siteInfo);
        $this->assign("configpri",$configpri );
        $this->assign("site_name",isset($siteInfo['site_name'])? $siteInfo['site_name']:'');
        $this->assign("current",'' );

        //等级
        $level=getLevelList();
        $levellist=array();
        foreach($level as $k=>$v){
            $levellist[$v['levelid']]=$v;
        }
        $levelanchor=getLevelAnchorList();
        $levelanchorlist=array();
        foreach($levelanchor as $k=>$v){
            $levelanchorlist[$v['levelid']]=$v;
        }

        $this->assign("levellist",$levellist );
        $this->assign("levellistj",json_encode($levellist) );
        $this->assign("levelanchorlist",$levelanchorlist );
        $this->assign("levelanchorlistj",json_encode($levelanchorlist) );

//        var_dump("HomeBase");
//        var_dump(session('uid'));

        if(session("uid")){
            $uid=session("uid");
            $token=session("token");

            $this->assign("user",getUserPrivateInfo($uid));
            $this->assign("userinfo",json_encode( getUserPrivateInfo($uid) ) );

        }else{
            $this->assign("user",[] );
            $this->assign("userinfo",'null' );
        }

        //读取国家代号
        $key='getCountrys';
        $info=getcaches($key);
        //$info=false;
        if(!$info){
            $country=CMF_ROOT.'data/config/country.json';
            // 从文件中读取数据到PHP变量
            $json_string = file_get_contents($country);
            // 用参数true把JSON字符串强制转成PHP数组
            $data = json_decode($json_string, true);
            $info=$data['country']; //国家
            setcaches($key,$info);
        }

        $country_list=[];
        $li_zg=[];
        foreach ($info as $k => $v) {
            $arr=$v['lists'];
            foreach ($arr as $k1 => $v1) {
				if($v1['tel']=='86'){
					$li_zg=$v1;
					unset($v1);
					continue;
				}
                $country_list[]=$v1;
            }
        }
		if($li_zg){
			array_unshift($country_list,$li_zg);
		}
        $this->assign("country_list",json_encode($country_list));
		$this->assign("countrys",$country_list);
    }

    protected function _initializeView(){

        $cmfThemePath    = config('template.cmf_theme_path');
        $cmfDefaultTheme = cmf_get_current_theme();
        $root            = cmf_get_root();
        $themePath       = "{$cmfThemePath}{$cmfDefaultTheme}";

        //使cdn设置生效
        $cdnSettings = cmf_get_option('cdn_settings');
        if (empty($cdnSettings['cdn_static_root'])) {
            $viewReplaceStr = [
                '__ROOT__'     => $root,
                '__TMPL__'     => "{$root}/{$themePath}",
                '__STATIC__'   => "{$root}/static",
                '__WEB_ROOT__' => $root
            ];
        } else {
            $cdnStaticRoot  = rtrim($cdnSettings['cdn_static_root'], '/');
            $viewReplaceStr = [
                '__ROOT__'     => $root,
                '__TMPL__'     => "{$cdnStaticRoot}/{$themePath}",
                '__STATIC__'   => "{$cdnStaticRoot}/static",
                '__WEB_ROOT__' => $cdnStaticRoot
            ];
        }

        $this->view->engine()->config([
            'view_base'          => WEB_ROOT.'../'. $themePath . '/',
            'tpl_replace_string' => $viewReplaceStr
        ]);

        /*$themeErrorTmpl = "{$themePath}/error.html";
        if (file_exists_case($themeErrorTmpl)) {
            config('dispatch_error_tmpl', $themeErrorTmpl);
        }

        $themeSuccessTmpl = "{$themePath}/success.html";
        if (file_exists_case($themeSuccessTmpl)) {
            config('dispatch_success_tmpl', $themeSuccessTmpl);
        }*/


    }

    /**
     * 加载模板输出
     * @access protected
     * @param string $template 模板文件名
     * @param array  $vars     模板输出变量
     * @param array  $config   模板参数
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $config = [])
    {
        $template = $this->parseTemplate($template);
        $more     = $this->getThemeFileMore($template);
        $this->assign('theme_vars', $more['vars']);
        $this->assign('theme_widgets', $more['widgets']);
        $content        = $this->view->fetch($template, $vars, $config);
        $designingTheme = cookie('cmf_design_theme');

        if ($designingTheme) {
            $app        = $this->app->http->getName();
            $controller = $this->request->controller();
            $action     = $this->request->action();

            $output = <<<hello
<script>
var _themeDesign=true;
var _themeTest="test";
var _app='{$app}';
var _controller='{$controller}';
var _action='{$action}';
var _themeFile='{$more['file']}';
if(parent && parent.simulatorRefresh){
  parent.simulatorRefresh();  
}
</script>
hello;

            $pos = strripos($content, '</body>');
            if (false !== $pos) {
                $content = substr($content, 0, $pos) . $output . substr($content, $pos);
            } else {
                $content = $content . $output;
            }
        }

        return $content;
    }

    /**
     * 自动定位模板文件
     * @access private
     * @param string $template 模板文件规则
     * @return string
     */
    protected function parseTemplate($template)
    {
        $siteInfo = cmf_get_site_info();
        $this->view->assign('site_info', $siteInfo);

        // 分析模板文件规则
        $request = $this->request;
        // 获取视图根目录
        if (strpos($template, '@')) {
            // 跨模块调用
            list($module, $template) = explode('@', $template);
        }

        $cmfThemePath    = config('template.cmf_theme_path');
        $cmfDefaultTheme = cmf_get_current_theme();
        $themePath       = WEB_ROOT."../". "{$cmfThemePath}{$cmfDefaultTheme}/";

        //var_dump($themePath);

        // 基础视图目录
        $module = isset($module) ? $module : $this->app->http->getName();
        $path   = $themePath . ($module ? $module . DIRECTORY_SEPARATOR : '');

        $depr = config('view.view_depr');
        if (0 !== strpos($template, '/')) {
            $template   = str_replace(['/', ':'], $depr, $template);
            $controller = cmf_parse_name($request->controller());
            if ($controller) {
                if ('' == $template) {
                    // 如果模板文件名为空 按照默认规则定位
                    $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . cmf_parse_name($request->action(true));
                } elseif (false === strpos($template, $depr)) {
                    $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
                }
            }
        } else {
            $template = str_replace(['/', ':'], $depr, substr($template, 1));
        }

        return $path . ltrim($template, '/') . '.' . ltrim(config('view.view_suffix'), '.');
    }

    /**
     * 获取模板文件变量
     * @param string $file
     * @param string $theme
     * @return array
     */
    private function getThemeFileMore($file, $theme = "")
    {

        //TODO 增加缓存
        $theme = empty($theme) ? cmf_get_current_theme() : $theme;

        // 调试模式下自动更新模板
        if (APP_DEBUG) {
            $themeModel = new ThemeModel();
            $themeModel->updateTheme($theme);
        }

        $themePath = config('template.cmf_theme_path');
        $file      = str_replace('\\', '/', $file);
        $file      = str_replace('//', '/', $file);
        $webRoot   = str_replace('\\', '/', WEB_ROOT);
        $themeFile = str_replace(['.html', '.php', $themePath . $theme . "/", $webRoot], '', $file);

        $files = Db::name('theme_file')->field('more')->where('theme', $theme)
            ->where(function ($query) use ($themeFile) {
                $query->where('is_public', 1)->whereOr('file', $themeFile);
            })->select();

        $vars    = [];
        $widgets = [];
        foreach ($files as $file) {
            $oldMore = json_decode($file['more'], true);
            if (!empty($oldMore['vars'])) {
                foreach ($oldMore['vars'] as $varName => $var) {
                    $vars[$varName] = $var['value'];
                }
            }

            if (!empty($oldMore['widgets'])) {
                foreach ($oldMore['widgets'] as $widgetName => $widget) {

                    $widgetVars = [];
                    if (!empty($widget['vars'])) {
                        foreach ($widget['vars'] as $varName => $var) {
                            $widgetVars[$varName] = $var['value'];
                        }
                    }

                    $widget['vars'] = $widgetVars;
                    //如果重名，则合并配置
                    if (empty($widgets[$widgetName])) {
                        $widgets[$widgetName] = $widget;
                    } else {
                        foreach ($widgets[$widgetName] as $key => $value) {
                            if (is_array($widget[$key])) {
                                $widgets[$widgetName][$key] = array_merge($widgets[$widgetName][$key], $widget[$key]);
                            } else {
                                $widgets[$widgetName][$key] = $widget[$key];
                            }
                        }
                    }
                }
            }
        }

        return ['vars' => $vars, 'widgets' => $widgets, 'file' => $themeFile];
    }

    public function checkUserLogin($isreurl = false)
    {
        $refer  = $this->request->server('HTTP_REFERER');
        $userId = cmf_get_current_user_id();
        if (empty($userId)) {
            if ($isreurl !== false) {
                $tourl = cmf_url("user/Login/index", ['redirect' => $refer]);
            } else {
                $tourl = cmf_url("user/Login/index");
            }
            if ($this->request->isAjax()) {
                $this->error("您尚未登录", $tourl);
            } else {
                $this->redirect($tourl);
            }
        }
    }

}
