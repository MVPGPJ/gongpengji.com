<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 主题后台必须引入的组件
 */

require_once("libs/Settings.php");
require_once("libs/I18n.php");
require_once("libs/Handsome.php");
require_once("libs/admin/FormElements.php");
require_once("libs/Lang.php");
require_once("libs/Content.php");
require_once("libs/Utils.php");
require_once("libs/Config.php");
require_once("libs/Ajax.php");
require_once("libs/UA.php");


/*表单组件*/
require_once ('libs/admin/Checkbox.php');
require_once ('libs/admin/Text.php');
require_once ('libs/admin/Radio.php');
require_once ('libs/admin/Select.php');
require_once ('libs/admin/Textarea.php');

/**
 * 主题初始化设置
 * @param $archive
 */
function themeInit($archive)
{


    Helper::options()->commentsMaxNestingLevels = 999;
    $options = mget();

    Handsome::$version = Handsome::returnHandsomeVersion();
    if (strtoupper($options->language) != "AUTO") {
        I18n::setLang($options->language);
    }
    Helper::options()->commentsAntiSpam = true;//强制开启后台反垃圾保护
    Helper::options()->commentsCheckReferer = false;//强制开启后台反垃圾保护
    Helper::options()->commentsOrder = 'DESC';//强制最新评论放在最前面
    if (!defined('THEME_URL')){//主题目录的绝对地址
        define("THEME_URL", rtrim(preg_replace('/^'.preg_quote($options->siteUrl, '/').'/', $options->rootUrl.'/', $options->themeUrl, 1),'/').'/');
    }

//    约定，定义所有的url末尾都要有斜杆
    if (!defined("BLOG_URL")){
        define("BLOG_URL",$options->rootUrl."/");
    }


    if ($options->rewrite == 1){//定义博客php的地址，判断是否开启了伪静态
        if(!defined('BLOG_URL_PHP'))
            define("BLOG_URL_PHP",BLOG_URL);
    }else{
        if(!defined('BLOG_URL_PHP'))
            define("BLOG_URL_PHP",BLOG_URL.'index.php/');
    }

    if (strlen(trim($options->LocalResourceSrc)) > 0){//主题静态资源的绝对地址
        @define('STATIC_PATH',$options->LocalResourceSrc);
    }else{
        @define('STATIC_PATH',THEME_URL.'assets/');
    }

    @define("PJAX_ENABLED", in_array('isPjax', $options->featuresetup));
    @define("PJAX_COMMENT_ENABLED", in_array('ajaxComment', $options->featuresetup));
    @define('IS_TOC',in_array('tocThree',$options->featuresetup));

    if ($options->commentChoice =='0') {
        @define("COMMENT_SYSTEM", Handsome_Config::COMMENT_SYSTEM_ROOT);
    } else if ($options->commentChoice =='1') {
        @define("COMMENT_SYSTEM", Handsome_Config::COMMENT_SYSTEM_CHANGYAN);
    } else if ($options->commentChoice =='2'){
        @define("COMMENT_SYSTEM", Handsome_Config::COMMENT_SYSTEM_OTHERS);
    }else{//空值的时候默认使用原生评论
        @define("COMMENT_SYSTEM", Handsome_Config::COMMENT_SYSTEM_ROOT);
    }

    if ($options->resolveMusicWay == '0'){
        @define('RESOLVE_MUSIC_WAY',0);
    }else if ($options->resolveMusicWay == '1'){
        @define('RESOLVE_MUSIC_WAY',1);
    }else{
        @define('RESOLVE_MUSIC_WAY',0);
    }

    if ($options->musicUrl != ""){
        @define('PLAYER_MUSIC_URL',$options->musicUrl);
    }else{
        @define('PLAYER_MUSIC_URL',"http://music.163.com/#/my/m/music/playlist?id=946995803");
    }

    switch ($options->publicCDNSelcet){
        case 0:
            @define('PUBLIC_CDN',serialize(Handsome_Config::$BOOT_CDN));
            @define('PUBLIC_CDN_PREFIX',"");
            break;
        case 1:
            @define('PUBLIC_CDN',serialize(Handsome_Config::$BAIDU_CDN));
            @define('PUBLIC_CDN_PREFIX',"");
            break;
        case 2:
            @define('PUBLIC_CDN',serialize(Handsome_Config::$SINA_CDN));
            @define('PUBLIC_CDN_PREFIX',"");
            break;
        case 3:
            @define('PUBLIC_CDN',serialize(Handsome_Config::$QINIU_CDN));
            @define('PUBLIC_CDN_PREFIX',"");
            break;
        case 4:
            @define('PUBLIC_CDN',serialize(Handsome_Config::$JSDELIVR_CDN));
            @define('PUBLIC_CDN_PREFIX',"");
            break;
        case 5:
            @define('PUBLIC_CDN',serialize(Handsome_Config::$CAT_CDN));
            @define('PUBLIC_CDN_PREFIX',"");
            break;
        case 6:
            @define('PUBLIC_CDN',serialize(Handsome_Config::$LOCAL_CDN));
            @define('PUBLIC_CDN_PREFIX',THEME_URL."assets/libs/");
            break;
        default:
            @define('PUBLIC_CDN',serialize(Handsome_Config::$LOCAL_CDN));
            @define('PUBLIC_CDN_PREFIX',THEME_URL."assets/libs/");
            break;
    }
    $stickyNum = 0;
    if (Utils::isPluginAvailable("Links_Plugin","Links")){
        $sticky = Helper::options()->plugin('Links')->sticky_cids;
        if(trim($sticky) != "" && $sticky !== null){
            $stickyNum = count(explode(',', strtr($sticky, ' ', ',')));
        }else{
            $stickyNum = 0;
        }
    }
    @define('INDEX_IMAGE_ARRAY',serialize(Utils::getImageNumRandomArray($options->pageSize + $stickyNum, Utils::getSj1ImageNum())));
    //右侧边栏图片
    @define('SIDEBAR_IMAGE_ARRAY',serialize(Utils::getImageNumRandomArray(5,Utils::getSj2ImageNum())));
}

/**
 * 主题后台配置
 * @param $form
 */
function themeConfig($form) {
    echo Handsome::styleoutput();
    echo Handsome::SettingsWelcome();
    I18n::loadAsSettingsPage(true);

    //TODO:标题1：主题外观

    if (false){
        echo Handsome::returnCheckHtml();
        echo <<<EOF
        <script>
          var inst = new mdui.Dialog('#example-4');
          inst.open();
        </script>
EOF;

    }else{
        $form->addItem(new CustomLabel('<div class="mdui-panel" mdui-panel="">'));
        $form->addItem(new Title('外观设置','外观设置开关、主题色调选择、盒子模型背景、背景透明度、背景颜色、渐变样式、Chrome地址栏颜色'));


        $indexsetup = new Checkbox('indexsetup',
            array(
                'header-fix' => _t('固定头部'),
                'aside-fix' => _t('固定导航'),
                'aside-folded' => _t('折叠导航'),
                'aside-dock' => _t('置顶导航'),
                'container-box' => _t('盒子模型'),
                'show-avatar' => _t('折叠左侧边栏头像'),
                'NoRandomPic-post' => _t('文章页面不显示头图'),
                'NoRandomPic-index' => _t('首页不显示头图'),
                'NoSummary-index' => _t('首页文章不显示摘要'),
                //'multiStyleThumb' => _t('首页文章为[两栏]样式'),
//            'crossStyleThumb' => _t("首页大图和小图交错显示"),
//            'smallStyleThumb' => _t('首页头图为[缩小版头图]样式'),
                'notShowRightSideThumb' => _t('右侧边栏不显示图片'),
                'opacityMode' => _t("炫酷透明模式(beta)"),
                'notShowleftBottomMenu' => '不显示左侧边栏底部菜单'
            ),
            array('header-fix', 'aside-fix','container-box'), _t('外观设置开关'),"* <code>盒子模型</code>即左右侧边栏居中，两侧留出空间 </br></br>* 
<code style='color: red'>炫酷透明模式</code>（必须看这里！！）：</br>1.必须在<code>主题色调选择</code>配置中选择".redText("第9种配色")."才能正常渲染。</br>2. 必须在 
<code>盒子模型中背景样式选择</code>配置中选择图片背景。</br>3. 必须在<code>背景颜色 / 图片</code>填写背景图片地址/颜色代码。</br> 4. 推荐配置 <code>透明模式的背景色</code> 
且该功能为实验功能，如有bug，请及时反馈");

        $form->addInput($indexsetup->multiMode());

        //首页头图样式选择
        $thumbStyle = new Radio('thumbStyle',array(
            '0' => _t("小头图模式"),
            '1' => _t("大头图模式"),
            '2' => _t("两种样式交错")
        ),"1","首页头图样式选择","小头图是很小正方形显示在文章介绍的左侧</br>大头图则是默认的样式</br>交错样式，则根据文章的奇偶顺序交错显示。</br><span style='color: red'>不管选择哪种，在文章页面还可以针对特定文章修改其样式</span>");
        $form->addInput($thumbStyle);


        //主题色调选择
        $themetype = new Radio('themetype',
            array(
                '0' => _t('1. black-white-black &emsp;&emsp;'),
                '1' => _t('2. dark-white-dark &emsp;&emsp;</br>'),
                '2' => _t('3. white-white-black &emsp;&emsp;'),
                '3' => _t('4. primary-white-dark &emsp;&emsp;</br>'),
                '4' => _t('5. info-white-black &emsp;&emsp;'),
                '5' => _t('6. success-white-dark &emsp;&emsp;</br>'),
                '6' => _t('7. danger-white-dark &emsp;&emsp;</br>'),
                '7' => _t('8. black-black-white &emsp;&emsp;</br>'),
                '8' => _t('9. dark-dark-light &emsp;&emsp;'),
                '9' => _t('10. info-info-light &emsp;&emsp;</br>'),
                '10' => _t('11. primary-primary-dark &emsp;&emsp;'),
                '11' => _t('12. info-info-black &emsp;&emsp;</br>'),
                '12' => _t('13. success-success-dark &emsp;&emsp;'),
                '13' => _t('14. danger-danger-dark &emsp;&emsp;</br>')
            ),

            //Default choose
            '7',_t('主题色调选择'),_t("</br>选择背景方案.如默认的<b>dark-white-dark</b> 分别代表：左侧边栏和上导航栏的交集部分、上导航栏、左侧边栏的颜色。")
        );
        $form->addInput($themetype);


        //盒子模型中背景样式选择
        $BGtype = new Radio('BGtype',
            array(
                '0' => _t('纯色背景 &emsp;'),
                '1' => _t('图片背景 &emsp;'),
                '2' => _t('渐变背景 &emsp;')
            ),

            //Default choose
            '0',_t('盒子模型中背景样式选择'),_t("<b style='color: red'>如果你没有选中“盒子模型”，该项不会生效。</b>选择背景方式,然后 对应填写下方的 '<b>背景颜色 / 图片</b>' 或选择 '<b>渐变样式</b>', 这里默认使用纯色背景.")
        );
        $form->addInput($BGtype);

        //盒子模型种背景颜色/图片填写
        $bgcolor = new Text('bgcolor', NULL, "#EFEFEF", _t('背景颜色 / 图片'), _t('<b style="color: red">如果你没有选中“盒子模型”，请忽略该项。</b><br /><b>盒子模型中背景样式选择</b>中选择纯色背景, 这里就填写颜色代码; <br /><b>盒子模型中背景样式选择</b>中选择了图片背景, 这里就填写图片地址;<br />'));
        $form->addInput($bgcolor);
        $bgcolor_mobile = new Text('bgcolor_mobile', NULL, "#EFEFEF", _t('手机模式下的背景颜色 / 图片'), _t('<b style="color: red">如果你没有选中“盒子模型”，请忽略该项。</b><br /><b>盒子模型中背景样式选择</b>中选择纯色背景, 这里就填写颜色代码; <br /><b>盒子模型中背景样式选择</b>中选择了图片背景, 这里就填写图片地址;<br />'));
        $form->addInput($bgcolor_mobile);

        //盒子模型中渐变样式选择
        $GradientType = new Radio('GradientType',
            array(
                '0' => _t('1. Aerinite &emsp;'),
                '1' => _t('2. Ethereal &emsp;'),
                '2' => _t('3. Patrichor <br />'),
                '3' => _t('4. Komorebi &emsp;'),
                '4' => _t('5. Crepuscular &emsp;'),
                '5' => _t('6. Autumn <br />'),
                '6' => _t('7. Shore &emsp;'),
                '7' => _t('8. Horizon &emsp;'),
                '8' => _t('9. Green Beach <br />'),
                '9' => _t('10. Virgin <br />'),
            ),

            '3', _t('渐变样式'), _t("<b>如果你没有选中“盒子模型”，请忽略该项。</b><br />如果选择渐变背景, 在这里选择想要的渐变样式.")
        );
        $form->addInput($GradientType);

        $opcityColor = new Text('opcityColor',NULL,"",_t("透明模式的背景颜色"),_t("该配置只有在启用了 <code>炫酷透明模式</code>才会生效。该配置项填写颜色，比如 
<code>rgba(0,0,0,0.3)</code>，填写后你就会发现效果的，就是让透明色不太过透明，导致字都看不清了。</br></br> 一般来说，".redText('根据背景图片的主色调来填比较好看。')." </br>举例：背景图片主色调是蓝色，那就填 <code>rgba(56, 136, 255, 0.3)</code> (56,136,255) 就是蓝色，0.3 代表背景的透明度。"));
        $form->addInput($opcityColor);

        //chrome 安卓选项卡颜色
        $ChromeThemeColor = new Text('ChromeThemeColor', NULL, _t('#3a3f51'), _t('Android Chrome 地址栏颜色'), _t('安卓系统下的chrome浏览器顶部的地址栏颜色，请填写正确的颜色代码。'));
        $form->addInput($ChromeThemeColor);

        //选择代码高亮的风格
        $codeStyle = new Radio('codeStyle',
            array(
                'vs' => 'vs风格（默认）',
                'zenburn' => 'zenburn（深黑）',
                'custom' => '自定义'
            ),'vs','代码高亮的风格选择','使用的代码高亮风格见 <a href="https://highlightjs.org/">风格列表</a>，系统默认内置 <code>vs.css</code> 和 <code>zenburn.css</code>两种。</br> 您可以在<a href="https://github.com/isagalaev/highlight.js/tree/master/src/styles">该网站</a>下载css风格文件，然后填写在 <code>assets/css/features/code/custom.min.css</code> 内，并且选择 <code>自定义</code>选项即可。');

        $form->addInput($codeStyle);

        $postFontSize = new Text("postFontSize",NULL,"14","文章内容字体大小","默认14，数字越大，字体越大");

        $form->addInput($postFontSize);

        //标题2：个人信息

        $form->addItem(new EndSymbol(2));
        $form->addItem(new Title('初级设置','首页名称、首页标题后缀、博主的名称、博主的介绍、首页一行文字介绍、favicon地址、头像图片地址、博客公告消息'));
        //$form->addItem(new Typecho_Widget_Helper_Layout("div",array("class"=>"titleStart")));


        //首页名称
        $IndexName = new Text('IndexName', NULL, '友人C', _t('首页名称'), _t('首页显示的名称，不是标题，显示在顶部导航栏的左侧。标题需要在<code>后台设置——基本设置</code>里修改，博客标题会显示在首页的头像的右侧'));
        $form->addInput($IndexName);
        $indexNameIcon = new Text("indexNameIcon",NULL,"",_t("首页名称左侧的图标"),"<strong style='color: red'>填写举例：<code>glyphicon glyphicon-eur</code></strong></br>首页标题左侧的图标，所有可用的图标列表详见<a href='https://handsome.ihewro.com/#/icons'>图标列表</a>");
        $form->addInput($indexNameIcon);
        //博客logo
        $logo = new Text('logo', NULL,NULL, _t('博客logo的HTML结构'), _t('<strong style="color: red">填写HTML代码，填写此处将不会显示上面设置中的博客名称和图标</strong>，如果是图片请填写下面格式 <code>   <img src="https://s2.ax1x.com/2019/07/20/ZzabDS.png">    </code>，也可以填写更复杂的html代码，如svg等'));
        $form->addInput($logo);
        //博客标题后缀
        $titleintro = new Text('titleintro', NULL,NULL, _t('博客标题后缀'), _t('你的博客标题栏博客名称后面的副标题，如果为空，则不显示副标题</br></br>'));
        $form->addInput($titleintro);
        //博主名称：aside.php中会调用
        $BlogName = new Text('BlogName', NULL, 'ihewro', _t('博主的名称'), _t('输入你的名称，左侧边栏头像下面会输出该名称'));
        $form->addInput($BlogName);
        //博主职业
        $BlogJob = new Text('BlogJob', NULL, 'A student', _t('博主的介绍'), _t('输入你的简介，在侧边栏的名称下面和时光机页面显示'));
        $form->addInput($BlogJob);
        //首页文字：将会在首页博客名称下面和404.php页面调用此字段
        $Indexwords = new Text('Indexwords', NULL, '迷失的人迷失了，相逢的人会再相逢', _t('首页一行文字介绍'), _t('输入你喜欢的一行文字吧，在首页显示'));
        $form->addInput($Indexwords);
        //favicon图标
        $favicon = new Text('favicon', NULL, NULL, _t('favicon 地址'), _t('填入博客 favicon 的地址, 不填则显示主机根目录下的favicon.ico文件'));
        $form->addInput($favicon);
        //博主头像：在本主题中首页index.php 和 aboutme.php中将会调用此头像
        $BlogPic = new Text('BlogPic', NULL, 'https://s2.ax1x.com/2019/07/20/ZzawN9.jpg', _t('头像图片地址'), _t('logo头像地址，尺寸在200X200左右即可,会在首页的 <code>左侧边栏中</code> 显示。'));
        $form->addInput($BlogPic);

        //博客公告消息
        $blogNotice = new Textarea('blogNotice', NULL, NULL, _mt('博客公告消息'), _mt('显示在博客页面顶端的一条消息。'));
        $form->addInput($blogNotice);
        //博客开始时间
        $startTime = new Textarea('startTime',NULL,NULL,_mt("博客开始时间"),_mt("博客开始的时间，用于计算博客的运行时间，显示在左侧边栏的[运行时间]栏中。</br> <span style='color: red'>请务必填写如下格式的时间：<code>2016-12-12</code> <code>2018-01-01</code></span>"));
        $form->addInput($startTime);
        //点击头像的链接地址
        $clickAvatarLink = new Textarea('clickAvatarLink',NULL,NULL,_mt("点击首页左侧边栏头像的跳转地址"),'v4.4版本以前，点击头像地址固定为cross.html，现在可以自定义该处的地址。</br>url地址请包含<code>https</code> 或 <code>http</code> 
协议头。</br>如果为空，则默认是 <code>xxx.com/cross.html</code>默认的地址为时光机的地址，请看  <a href="https://handsome.ihewro.com/#/page">使用文档——独立页面（时光机）</a> ');
        $form->addInput($clickAvatarLink);

        //标题三：高级选项

        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Title('高级设置','支付宝二维码、微信二维码、时光机页面的头图、时光机中关于我的内容、时光机社交按钮配置、时光机联系方式配置、左侧边栏导航配置、顶部导航按钮配置'));
        //$form->addItem(new Typecho_Widget_Helper_Layout("div",array("class"=>"titleStart")));

        //大图版式头图下自定义摘要
        $numberOfBigPic = new Text('numberOfBigPic',NULL,'200',_t('大图版式头图下自定义摘要字数'),_t('默认200字（推荐）'));
        $form->addInput($numberOfBigPic);
        //小图版式头图下自定义摘要
        $numberOfSmallPic = new Text('numberOfSmallPic',NULL,'80',_t('小图版式头图下自定义摘要字数'),_t('默认80字（推荐小于80字）'));
        $form->addInput($numberOfSmallPic);
        //支付宝二维码
        $AlipayPic = new Text('AlipayPic', NULL, 'https://s2.ax1x.com/2019/07/20/ZzagBD.jpg', _t('支付宝二维码'), _t('打赏中使用的支付宝二维码,建议尺寸小于250×250,且为正方形'));
        $form->addInput($AlipayPic);
        //微信二维码
        $WechatPic = new Text('WechatPic', NULL, 'https://s2.ax1x.com/2019/07/20/ZzacnO.png', _t('微信二维码'), _t('打赏中使用的支付宝二维码,建议尺寸小于250×250,且为正方形'));
        $form->addInput($WechatPic);
        $payTips = new Text("payTips",NULL,"如果觉得我的文章对你有用，请随意赞赏","文章页面打赏默认提示文字","默认为“如果觉得我的文章对你有用，请随意赞赏”");
        $form->addInput($payTips);
        //左侧边栏导航自定义
        //error_reporting(0);
        $asideItems = new Textarea('asideItems', NULL, NULL, _mt('左侧边栏导航').' <a href="https://handsome.ihewro.com/#/customize" target="_blank"><span style="">'._mt('配置文档').'</span></a>', '<span style="color:red;">如果不明白此项，请清空该项</span>');
        $form->addInput($asideItems);
        //顶部导航按钮自定义
        $headerItems = new Textarea('headerItems', NULL, NULL, _mt('顶部导航按钮配置').' <a href="https://handsome.ihewro.com/#/customize" target="_blank"><span style="">'._mt('配置文档').'</span></a>','<span style="color:red;">如果不明白此项，请清空该项</span>');
        $form->addInput($headerItems);
        $open_new_world = new Text('open_new_world',NULL,NULL,"全站访问密码","填写密码，即可全站加密访问，即必须填写正确的密码才可以访问。<span style='color: red'>如果不想加密访问，请清空该项</span>");
        $form->addInput($open_new_world);

        //标题五：时光机配置
        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Title('时光机配置','头图、关于我内容、社交按钮配置、联系方式配置、身份验证编码、RSS展示配置'));
        //时光机页面的头图
        $timepic = new Text('timepic', NULL, 'https://s2.ax1x.com/2019/07/20/Zza59I.png', _t('时光机页面的头图'), _t("填写图片地址，在时光机页面cross.html独立页面的头图，图片大小切勿过大，控制在100K左右为佳。"));
        $form->addInput($timepic);
        //时光机中关于我的内容
        $about = new Textarea('about', NULL, '来自南部的一个小城市，个性不张扬，讨厌随波逐流。', _t('时光机中关于我内容'), _t('输入关于我的内容，将会在时光机的关于我栏目中显示'));
        $form->addInput($about);
        $socialItems = new Textarea('socialItems', NULL, '{"name":"twitter","class":"fontello fontello-twitter","link":"#"},
{"name":"facebook","class":"fontello fontello-facebook","link":"#"},
{"name":"googlepluse","class":"fontello fontello-google-plus","link":"#"},
{"name":"github","status":"single","link":"#"}', _mt('时光机社交按钮配置').' <a href="https://handsome.ihewro.com/#/customize" target="_blank"><span style="">'._mt('配置文档').'</span></a>', _mt('基本配置直接修改框中的value值和link值即可，高级配置请看文档'));
        $form->addInput($socialItems);
        $contactItems = new Textarea('contactItems', NULL, '{"name":"email","img":"https://s2.ax1x.com/2019/07/20/ZzU1sO.png","value":"你的邮箱地址","link":"#"},
{"name":"QQ","img":"https://s2.ax1x.com/2019/07/20/ZzUlQK.png","value":"你的QQ号","link":"#"},
{"name":"微博","img":"https://s2.ax1x.com/2019/07/20/ZzUMz6.png","value":"你微博账号","link":"#"},
{"name":"网易云音乐","img":"https://s2.ax1x.com/2019/07/20/ZzU3LD.png","value":"你的网易云账号","link":"#"}', _mt('时光机联系方式配置').' <a href="https://handsome.ihewro.com/#/customize" target="_blank"><span style="">'._mt('配置文档').'</span></a>', _mt('基本配置直接修改框中的value值和link值即可，高级配置请看文档'));
        $form->addInput($contactItems);
        //时光机配置码
        $time_code = new Text('time_code',NULL,NULL,"时光机身份验证编码","用于微信公众号发送时光机验证个人身份的编码，请勿告诉任何人。如果编码泄露，别人可以通过该编码在微信公众号向的博客添加说说。你可以随时更新此编码，以便不被别人使用。<a href='https://handsome.ihewro.com/#/wechat'>图文教程</a>");
        $form->addInput($time_code);

        $rssItems = new Textarea('rssItems', NULL, '', _mt('RSS动态内容配置').' <a href="https://handsome.ihewro.com/#/rss" target="_blank"><span style="">'._mt('配置文档').'</span></a>', _mt('万物皆可 RSS，可直接使用DIYGOD开源的服务，详细配置请看文档'));
        $form->addInput($rssItems);
        $timeHistory = new Select("timeHistory",array(
            2=>"关闭",
            1=>"开启",
        ),"2","那年今日","开启后，会在时光机页面显示过去近40个月的同日的内容。</br>适合已经记录了很长时间的博客，对于刚使用时光机的用户建议关闭");
        $form->addInput($timeHistory);


        //标题四：评论设置
        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Title('评论设置','评论系统选择、评论框背景设置、默认gravatar头像、填写畅言appid、conf'));
        //$form->addItem(new Typecho_Widget_Helper_Layout("div",array("class"=>"titleStart")));

        //评论系统选择
        $commentChoice = new Radio('commentChoice',
            array(
                '0' => _t('原生评论'),
                '1' => _t('畅言'),
                '2' => _t('其他第三方评论系统')
            ),
            //Default choose
            '0',_t('评论系统设置'),_t('推荐使用原生评论。如使用畅言，请在下方填写参数。如使用其他第三方评论需要手动往模板目录下的component/third_party_comments.php里添加第三方评论代码')
        );
        $form->addInput($commentChoice);
        //评论框位置
        $commentPosition = new Radio('commentPosition',
            array(
                'top' => _t('顶部'),
                'bottom' => _t('底部')
            ),
            //Default choose
            'bottom',_t('评论框位置'),_t('顶部即评论框在评论列表前，底部即评论框在评论列表后（默认）')
        );
        $form->addInput($commentPosition);
        $commentTips = new Text("commentTips",null,"使用cookie技术保留您的个人信息以便您下次快速评论，继续评论表示您已同意该条款","评论框提示文字","显示在「发表评论」的右侧");
        $form->addInput($commentTips);
        //评论框背景图片
        $commentBackground = new Text('commentBackground',NULL, 'https://s2.ax1x.com/2019/07/20/ZzaGcV.png',_t('原生评论框的背景图片'),_t('建议填写背景透明的png格式图片'));
        $form->addInput($commentBackground);
        //默认gravator头像
        $defaultAvator = new Text('defaultAvator',NULL, NULL ,_t('默认gravatar头像'),_t('gravatar的默认头像'));
        $form->addInput($defaultAvator);
        //畅言appKey
        $ChangyanAppKey = new Text('ChangyanAppKey', NULL, NULL , _t('填写畅言appid'), _t('填写你的畅言appid'));
        $form->addInput($ChangyanAppKey);
        //畅言conf
        $ChangyanConf = new Text('ChangyanConf', NULL, NULL, _t('填写畅言conf'), _t('请在这里填写您的畅言conf参数'));
        $form->addInput($ChangyanConf);

        //标题五：主题增强功能

        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Title('主题增强功能','增强功能开关、文章头图随机图数量、右侧边栏随机缩略图数量、博客头图来源设置、云音乐播放歌单设置、界面语言'));
        //$form->addItem(new Typecho_Widget_Helper_Layout("div",array("class"=>"titleStart")));

        $featuresetup = new Checkbox('featuresetup',
            array(
                'musicplayer' => _t('启用音乐播放器'),
                'isPjax' => _t('启用pjax'),
                'payforauthorinpost' => _t('启用文章打赏功能'),
                'payforauthorinpage' => _t('启用独立页面打赏功能'),
                'laodthefont' => _t('加载主题内置的英文字体'),
                'smoothscroll' => _t('启用页面平滑滚动'),
                'ajaxComment' => _t('启用ajax评论（当评论有问题时，请关闭该项查看错误信息）'),
                'tocThree' => _t('启用目录树（同时 <code>启用文章页面（包括独立页面）显示动画</code>会导致目录树不跟随，暂不能解决   ）'),
                'FixedImageSize' => _t('固定头图大小比例 8:3（自动裁剪）'),
                'emailToQQ' => _t('评论者QQ邮箱解析为QQ头像'),
                'hideLogin' => _t('隐藏主题中所有登录注册地址'),
                'hightlightcode' => _t('启用主题内置的代码高亮（支持19种常用语言）'),
                'showSettingsButton' => _t('显示主题右侧边栏的设置按钮'),
                'mathJax' => _t('启用公式（MathJax）'),
                'hitokoto' => _t('首页的标题栏下启用一言接口(启用该功能代表自定义文字功能失效)'),
                'isPageAnimate' => _t('启用文章页面（包括独立页面）显示动画'),
                'isOtherAnimate' => _t('启用非文章页面（如首页、归档）显示动画'),
                'lazyload' => _t('延迟加载图片（lazyload）不推荐开，会带来卡顿'),
                'sreenshot' => _t("启用文章页面截图(beta)"),
                "openCategory" => _t("首页左侧边栏默认展开分类")
            ),
            array('musicplayer','isPjax','payforauthorinpost','laodthefont','smoothscroll','tocThree','ajaxComment','FixedImageSize','hightlightcode'), _t('增强功能开关'),"* <code>固定头图大小比例 8:3（自动裁剪）</code>功能：只是让显示的尺寸符合8：3，实际体积大小并没有变化。</br>* <code>启用文章页面截图</code>功能：会引入160 KB的html2canvas.min.js");
        $form->addInput($featuresetup->multiMode());
        //markdown语法扩展
        $markdownExtend = new Checkbox('markdownExtend',array(
            'scode'=>'添加文字着重强调书写方式',
            'pinyin' =>'添加 {{拼音 : pin yin}} 语法解析注音'
        ),array('scode,pinyin'),'markdown扩展设置','<code>!>(空格)文字</code> 表示黄色警告框<br /><code>i>(空格)文字</code> 表示蓝色信息框<br /><code>@>(空格)文字</code> 表示银色引用框<br /><code>x>(空格)文字</code> 表示红色错误框<br /><code>√>(空格)文字</code> 表示绿色成功框<br />');
        $form->addInput($markdownExtend->multiMode());


        //文章缩略图设置
        $RandomPicChoice = new Radio('RandomPicChoice',
            array(
                '0' => _t('1.只显示随机图片</br>'),
                '1' => _t('2.显示顺序：thumb自定义字段——文章第一个附件——文章第一张图片</br>'),
                '2' => _t('3.显示顺序：thumb自定义字段——文章第一个附件——文章第一张图片——随机图片(推荐)</br>'),
                '3' => _t('4.显示顺序：thumb自定义字段——随机图片')
            ),
            //Default choose
            '2',_t('博客头图来源设置'),_t('该头图来源设置对首页和文章页面同时生效。推荐选择第三个。<br><span style="color: #f00">注意</span>：此项设置仅在开启显示头图后才生效')
        );
        $form->addInput($RandomPicChoice);

        //文章缩略图设置
        $hotPostOrderType = new Radio('hotPostOrderType',
            array(
                'commentsNum' => _t('按照评论数排序'),
                'views' => _t('按照浏览数排序')
            ),
            //Default choose
            'commentsNum',_t('热门文章的排序规则'),_t('默认按照评论数排序，有个别小伙伴由于当地网安无法开启评论，故增加了该选项')
        );
        $form->addInput($hotPostOrderType);

        //全局播放器设置
        $playerSetting = new Checkbox('playerSetting',
            array(
                //'mobileShow' => '手机端显示播放器',
                'autoPlay' => '音乐自动播放（为了用户体验，仅在电脑端有效）'

            ),'','全局播放器设置','只有当启用了音乐播放器，该设置项才有效');
        $form->addInput($playerSetting);

        //解析音乐方式
        $resolveMusicWay = new Radio('resolveMusicWay',
            array(
                '0' => '云解析歌单',
                '1' => '自定义解析内容'
            ),'0','解析音乐方式选择','某些国外服务器不支持云解析歌单的功能，如果使用音乐播放器，请选择 <code>自定义解析内容</code>，并在 <code>音乐播放器自定义解析内容</code>配置项中按照指定格式填写解析内容。 ');

        $form->addInput($resolveMusicWay);

        //播放器音乐
        $musicUrl = new Text('musicUrl',NULL,_t('https://y.qq.com/n/yqq/playlist/888233349.html'),_t('云解析歌单设置'),'支持 <code>网易云音乐</code>、<code>QQ音乐</code>、<code>虾米音乐</code>、<code>百度音乐</code>四家音乐媒体的<strong>歌单(不包括专辑，请注意)</strong>解析。</br>1. 网易云：如歌单地址： 
<code>http://music.163.com/#/my/m/music/playlist?id=883542351</code></br>2. 
QQ音乐：如歌单地址： <code>https://y.qq.com/n/yqq/playlist/1144188779.html</code></br>3. 虾米音乐：如歌单地址： <code>http://www.xiami.com/collect/254478782</code></br>4. 百度音乐：如歌单地址： <code>http://music.baidu.com/songlist/364201689</code></br>');
        $form->addInput($musicUrl);

        //自定义音乐内容
        $customMusicContent = new Textarea('customMusicContent',null,"",_t('音乐播放器自定义解析内容'),'某些国外服务器不支持云解析歌单的功能，如果使用音乐播放器请在该设置项中添加音频地址。(请在 <code>解析音乐方式选择</code>中选择 <code>自定义解析内容</code> )</br>格式: <code>{"name":"xxx", "author":"xxx", "src":"http://xxxx/xxx.mp3","cover":"xxx.com/xxx.jpg"}</code> </br>
每两项之间用英文逗号隔开。最后一项请勿添加逗号。<a href="https://handsome.ihewro.com/#/functions">使用文档</a> ');
        $form->addInput($customMusicContent);

        //语言设置
        $language = new Select('language', I18n_Options::listLangs(), 'auto','界面语言', '默认为自动, 即根据浏览器设置自动选择语言。');
        $form->addInput($language->multiMode());


        //标题六：PJAX

        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Title('PJAX','pjax回调函数'));
        //$form->addItem(new Typecho_Widget_Helper_Layout("div",array("class"=>"titleStart")));

        //动画选择

        $pjaxAnimate = new Select('pjaxAnimate',array(
            'default' =>'彩虹进度条',
            'minimal' => '普通进度条',
            'flash' => '普通进度条+圆形指示器',
            'big-counter' => '右上角数字百分比指示器',
            'corner-indicator' => '右上角圆形转圈指示器',
            'center-simple' => '中间指示器',
            'loading-bar' => '中间指示器+百分比计数',
            'whiteRound' => '素雅圆圈',
            'customise' => '自定义pjax动画（需要填写下面两个配置）'
        ),'default',_t('选择pjax动画'),_t('默认选择彩虹进度条'));
        $form->addInput($pjaxAnimate);

        $pjaxCusomterAnimateHtml = new Textarea("pjaxCusomterAnimateHtml",NULL,NULL,"自定义pjax动画的HTML结构","<span style='color: red'>只有在<code>pjax动画</code>选择自定义动画，才需要填写该项。如果你不会，请清空该项，无需填写</span><a href='https://handsome.ihewro.com/#/pjaxanimate'>使用文档</a>");
        $form->addInput($pjaxCusomterAnimateHtml);

        $pjaxCusomterAnimateCSS = new Textarea("pjaxCusomterAnimateCSS",NULL,NULL,"自定义pjax动画的CSS代码","<span style='color: red'>只有在<code>pjax动画</code>选择自定义动画，才需要填写该项。如果你不会，请清空该项，无需填写</span><a href='https://handsome.ihewro.com/#/pjaxanimate'>使用文档</a>");
        $form->addInput($pjaxCusomterAnimateCSS);

        $isPjaxShowMatte = new Select('isPjaxShowMatte',array(
            '0' =>'是',
            '1' => '否'
        ),'1',_t('pjax动画是否显示白色遮罩'),_t('默认选择是，显示白色遮罩，切换页面时候内容模块透明度会降低（选择彩色进度条，该项设置无效）'));
        $form->addInput($isPjaxShowMatte);

        $whiteOpacity = new Text('whiteOpacity', NULL, '0.4', _t('白色遮罩的透明度'), '填写0-1 的内的小数。值越小越透明。');
        $form->addInput($whiteOpacity);

        $isPjaxToTop = new Select('isPjaxToTop',array(
            '0' =>'是',
            '1' => '否'
        ),'0',_t('pjax切换页面是否返回顶部'),_t('默认选择是，则切换页面会先返回顶部再切换其他页面内容。你可以尝试选择否看到不同效果'));
        $form->addInput($isPjaxToTop);

        $toTopSpeed = new Text('toTopSpeed', NULL, '100', _t('返回顶部速度'), '填写0-1000 毫秒 的数字，数字越大，速度越慢。0代表直接返回顶部，没有任何缓冲时间。设置该项配置，<code>pjax切换页面是否返回顶部</code>配置需要选择是。');
        $form->addInput($toTopSpeed);

        $progressColor = new Text('progressColor', NULL, '#000000', _t('动画主体颜色'), '除彩虹进度条动画，其他动画都是单色，该项设置可以配置动画的主体颜色，请填写正确的颜色代码，如： <code>#000000</code>');
        $form->addInput($progressColor);

        //回调函数
        $ChangeAction = new Textarea('ChangeAction', NULL, NULL, _t('PJAX回调函数'), _t('如果你启用了pjax,当切换页面时候，js不会重写绑定事件到新生成的节点上。</br>你可以在该项设置中重新加载js函数，以便将事件正确绑定ajax生成的DOM节点上。</br>如果不明白，请留空该项。或者在 <code>主题增强功能</code>中取消启用pjax。'));
        $form->addInput($ChangeAction);


        //标题七：速度优化


        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Title('速度优化','将本地静态资源上传到你的cdn上、选择公共CDN库、图片附件镜像加速、DNS Prefetch加速、gravatar镜像源地址'));
        //$form->addItem(new Typecho_Widget_Helper_Layout("div",array("class"=>"titleStart")));

        $LocalResourceSrc = new Text('LocalResourceSrc', NULL, NULL, _t('将本地静态资源上传到你的cdn上'),_t('使用该项设置前，你必须有自己搭建的cdn服务器（不是指当前服务器）<a href="https://handsome.ihewro.com/#/speed">详细使用文档</a></br> 主题目录下的<code>/assets/</code>目录下有 
<code>css、js、fonts、img</code>四个静态资源文件夹。</br>你需要把<code>asset</code>目录上传到你的cdn服务器上，比如CDN服务器的 <code>handsome目录</code>里，地址即为 <code>https://cdn.ihewro.com/handsome/assets/</code></br>在当前框中就填入该地址，主题就会引用你搭建的cdn上面的资源，而不再引用当前服务器上的资源'));
        $form->addInput($LocalResourceSrc);

        //七牛云镜像存储
        $cdnAddress = new Text('cdn_add', NULL, NULL, _t('本地图片云存储(镜像)加速'), _t('通过各大服务商的镜像存储功能，将博客的本地图片（即在本服务器上的图片）自动替换为对应镜像空间的图片地址，而在第一次访问的时候，会自动将本地服务器图片上传到镜像空间（需要在服务商进行配置 <code>加速域名</code>）。</br>而通常访问云服务器的速度要比本服务器速度要快（因为云服务器具有cdn特性）。</br> 填写内容格式为 <code>自定义域名 | CDN
类型</code> </br> 举例： <code>https://assets.ihewro.com | UPYUN</code> 在服务商的镜像空间配置加速域名为 <code>https://www.ihewro.com</code> </br>   支持以下服务商：</br>七牛云「QINIU」</br>又拍云「UPYUN」</br>阿里云「ALIOSS」（暂不支持）</br>具体细节请看 <a href="https://handsome.ihewro.com/#/speed">详细使用文档</a>'));
        $form->addInput($cdnAddress);

        $cloudOptions = new Checkbox("cloudOptions",array(
            0 => '为文章中的图片自动转换合适的大小和格式',
            1 => '启用 WebP 图像格式'
        ),array(),_t("云存储选项"),"<span style='color: red'>使用该项配置前，需要先配置 <code>本地图片云存储(镜像)加速</code> </span></br>* 
我们使用的图片大小尺寸很多时候是大于所需要的尺寸（div的尺寸），造成图片加载缓慢，启动第一项配置会自动使用云存储服务商提供的图片处理对图片进行处理，以便加载更小的体积 
</br> * 
WebP 
是一种新的图片格式，最多可以减少70%的图片大小，目前只有chrome内核比较支持，safari和firefox 是无法显示图片的。</br>有些云服务商 <code>webp自适应开关</code>，开启后无需启用主题的 <code>启用 WebP 图像格式</code>选项，在支持的浏览器自动转换webp格式。 </br>具体细节请看 <a href=\"https://handsome.ihewro.com/#/functions\">详细使用文档</a>");
        $form->addInput($cloudOptions);

        $publicCDNSelcet = new Select('publicCDNSelcet',array(
            '0' =>'BootCDN',
            '1' => '百度静态资源公共库',
            //'2' => '新浪 SAE',
            '3' => '七牛云',
            '4' => 'jsDelivr',
            '5' => 'catCDN',
            '6' => '本地'
        ),'6',_t('选择公共CDN库'),_t('主题除了使用本地一些静态资源，还引用了bootstrap/jquery 库的公共cdn地址（体积较大>60kb），通常使用托管在公共cdn的资源会获得更快的访问速度</br>默认选择BootCDN，因为各大公共CDN库的目录结构有所差异，暂不支持自定义CDN库。
<br/><span style="color: red">注意：bootcdn在有些地区可能无法正常访问，此时你可以更换其他的公共CDN，比如百度或七牛，稳定性比较好。使用 <code>本地</code>表示直接加载你的服务器上面资源</span>'));
        $form->addInput($publicCDNSelcet);


        //dns 预加载
        $dnsPrefetch = new Textarea('dnsPrefetch', NULL, NULL, _mt('DNS Prefetch'), _mt('DNS 预读取是一种使浏览器主动执行 DNS 解析已达到优化加载速度的功能。<br>你可以在这里设置需要预读取的域名，<bold>每行一个，仅填写域名即可。</bold><br>如：img.example.com'));
        $form->addInput($dnsPrefetch);

        //gravatar镜像源
        $CDNURL = new Text('CDNURL', NULL, 'https://secure.gravatar.com/avatar', _t('gravatar镜像源地址'), _t("<span style='color: red'>*请勿为空</span></br>
    gravatar由于国内被墙，推荐使用https://secure.gravatar.com/avatar 或者https://cdn.v2ex.com/gravatar 镜像源。你可以使用你自己的镜像源(末尾不要加斜杠！)"));
        $form->addInput($CDNURL);

        //标题八：开发者设置


        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Title('开发者设置','博客底部左侧信息、博客底部右侧信息、自定义css、自定义js、网站统计代码、广告位设置'));
        // $form->addItem(new Typecho_Widget_Helper_Layout("div",array("class"=>"titleStart")));

        //网站底部左侧信息
        $BottomleftInfo = new Textarea('BottomleftInfo', NULL, NULL, _t('博客底部左侧信息'), _t('这里面填写的是 <code>html代码</code>，位置是博客底部的左边。</br>可以填写<span style="color: red">备案号</span>等一些信息。注意：所有屏幕尺寸下都会显示该内容'));
        $form->addInput($BottomleftInfo);
        //网站底部右侧信息
        $BottomInfo = new Textarea('BottomInfo', NULL, NULL, _t('博客底部右侧信息'), _t('这里面填写的是 <code>html代码</code>，位置是博客底部的右边。可以填写备案号等一些信息。</br>注意：屏幕尺寸小于767px，不会显示该内容'));
        $form->addInput($BottomInfo);
        //自定义css
        $customCss = new Textarea('customCss', NULL, NULL, _t('自定义 CSS'), _t('这里填写的是css代码，来进行自定义样式，会自动输出到<code><\/head></code>标签之前'));
        $form->addInput($customCss);
        //自定义js
        $customJs = new Textarea('customJs', NULL, NULL, _t('自定义 JavaScript'), _t('这里填写的是JavaScript代码，会自动输出到<code><\/body></code>标签之前'));
        $form->addInput($customJs);
        //自定义HTML
        $analysis = new Textarea('analysis', NULL, NULL, _t('自定义输出head 头部的HTML代码'), _t("这里填写的是html代码，会输入到<code><\/head></code>之前</br> 你可以填写<span style='color: red'>网站统计代码</span>等其他信息。</br> <code>网站统计代码</code>推荐google 统计和百度统计，不推荐cnzz（会导致样式错误，如果你会修改样式的话，请忽略该行）"));
        $form->addInput($analysis);

        $bottomHtml = new Textarea('bottomHtml', NULL, NULL, _t('自定义输出body 尾部的HTML代码'), _t("这里填写的是html代码，会输入到<code><\/body></code>之前</br>"));
        $form->addInput($bottomHtml);

        $adContentSidebar = new Textarea('adContentSidebar', NULL,NULL,_t('全局右侧边栏广告位'),_t('此处可以填写HTML代码，广告位的位置在右侧的边栏中。</br>关于投放google广告可以看这位博主的使用教程，我自己没有测试过<a href="https://www.imydl.tech/status/571.html">Typecho 下 Handsome 主题投放谷歌AdSense广告总结</a>'));
        $form->addInput($adContentSidebar);
        $adContentPost = new Textarea('adContentPost', NULL,NULL,_t('文章页脚广告位'),_t('此处可以填写HTML代码'));
        $form->addInput($adContentPost);
        $adContentPage = new Textarea('adContentPage', NULL,NULL,_t('独立页面页脚广告位'),_t('此处可以填写HTML代码'));
        $form->addInput($adContentPage);


        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));



        $form->addItem(new Title('页面元素显示设置','更加自由方便的控制主题的某些模块的显示，如需更多需求，请自行修改代码。'));

        //左侧边栏
        $asideSetting = new Checkbox('asideSetting',
            array(
                'component' => '[组成]不显示'

            ),'','左侧边栏元素控制','控制显示左侧边栏的一些模块');
        $form->addInput($asideSetting);

        $sidebarSetting = new Checkbox('sidebarSetting',
            array(
                'all' => '[右侧边栏整体]不显示',
                'info' => '[博客信息]不显示'

            ),'','右侧边栏元素控制','控制显示右侧边栏的一些模块');
        $form->addInput($sidebarSetting);

        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));
        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));

        //mdui-panel 结束符
        $form->addItem(new Typecho_Widget_Helper_Layout("/div"));

        $submit = new Typecho_Widget_Helper_Form_Element_Submit(NULL, NULL, _t('保存设置'));
        $submit->input->setAttribute('class', 'mdui-btn mdui-color-theme-accent mdui-ripple submit_only');
        $form->addItem($submit);

    }


}




//文章页面侧边栏缩略图
function showSidebarThumbnail($widget, $index = 0)
{

    $randomNum = unserialize(SIDEBAR_IMAGE_ARRAY);
    $random = THEME_URL . 'usr/img/sj2/' . $randomNum[$index] . '.jpg'; // 随机缩略图路径
    //正则匹配 主题目录下的/images/sj2/的图片（以数字按顺序命名）

    return $random;
}

/**
 * 显示上一篇
 *
 * @access public
 * @param string $default 如果没有上一篇,显示的默认文字
 * @return void
 * @throws Typecho_Db_Exception
 */
function theNext($widget, $default = NULL)
{
    $db = Typecho_Db::get();
    $sql = $db->select()->from('table.contents')
        ->where('table.contents.created > ?', $widget->created)
        ->where('table.contents.created < ?', time())
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.type = ?', $widget->type)
        ->where('table.contents.password IS NULL')
        ->order('table.contents.created', Typecho_Db::SORT_ASC)
        ->limit(1);
    $content = $db->fetchRow($sql);

    if ($content) {
        $content = $widget->filter($content);
        $link = '<li class="previous"> <a href="' . $content['permalink'] . '" title="' . $content['title'] . '" data-toggle="tooltip"> '._mt("上一篇").' </a></li>
';
        echo $link;
    } else {
        $link = '';
        echo $link;
    }
}

/**
 * 显示下一篇
 *
 * @access public
 * @param string $default 如果没有下一篇,显示的默认文字
 * @return void
 */
function thePrev($widget, $default = NULL)
{
    $db = Typecho_Db::get();
    $sql = $db->select()->from('table.contents')
        ->where('table.contents.created < ?', $widget->created)
        ->where('table.contents.created < ?', time())
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.type = ?', $widget->type)
        ->where('table.contents.password IS NULL')
        ->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->limit(1);
    $content = $db->fetchRow($sql);

    if ($content) {
        $content = $widget->filter($content);
        $link = '<li class="next"> <a href="' . $content['permalink'] . '" title="' . $content['title'] . '" data-toggle="tooltip"> '._mt("下一篇").' </a></li>';
        echo $link;
    } else {
        $link = '';
        echo $link;
    }
}



//获取评论的锚点链接
function get_comment_at($coid)
{
    $db   = Typecho_Db::get();
    $prow = $db->fetchRow($db->select('parent,status')->from('table.comments')
        ->where('coid = ?', $coid));//当前评论
    $mail = "";
    $parent = @$prow['parent'];
    if ($parent != "0") {//子评论
        $arow = $db->fetchRow($db->select('author,status,mail')->from('table.comments')
            ->where('coid = ?', $parent));//查询该条评论的父评论的信息
        @$author = @$arow['author'];//作者名称
        $mail = @$arow['mail'];
        if(@$author && $arow['status'] == "approved"){//父评论作者存在且父评论已经审核通过
            if (@$prow['status'] == "waiting"){
                echo '<p class="commentReview">'._mt("（评论审核中）").'</p>';
            }
            echo '<a href="#comment-' . $parent . '">@' . $author . '</a>';
        }else{//父评论作者不存在或者父评论没有审核通过
            if (@$prow['status'] == "waiting"){
                echo '<p class="commentReview">'._mt("（评论审核中）").'</p>';
            }else{
                echo '';
            }

        }

    } else {//母评论，无需输出锚点链接
        if (@$prow['status'] == "waiting"){
            echo '<p class="commentReview">'._mt("（评论审核中）").'</p>';
        }else{
            echo '';
        }
    }

    return $mail;


}

//文章阅读次数含cookie
function get_post_view($archive)
{
    $cid    = $archive->cid;
    $db     = Typecho_Db::get();
    $prefix = $db->getPrefix();
    if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents')))) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) DEFAULT 0;');
        return 0;
    }
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    if ($archive->is('single')) {
        $views = Typecho_Cookie::get('extend_contents_views');
        if(empty($views)){
            $views = array();
        }else{
            $views = explode(',', $views);
        }
        if(!in_array($cid,$views)){//如果cookie不存在才会加1
            $db->query($db->update('table.contents')->rows(array('views' => (int) $row['views'] + 1))->where('cid = ?', $cid));
            array_push($views, $cid);
            $views = implode(',', $views);
            Typecho_Cookie::set('extend_contents_views', $views); //记录查看cookie
        }
    }
    return $row['views'];
}


//获得读者墙
function getFriendWall()
{   $options = Typecho_Widget::widget('Widget_Options');
    $db = Typecho_Db::get();
    $period = time() - 604800; // 单位: 秒, 时间范围: 7天
    $avatar = "";
    $guestDes = "";
    $color = array("bg-danger","bg-info","bg-warning");
    //总排行榜
    $sql_7days = $db->select('COUNT(author) AS cnt', 'author', 'max(url) url', 'max(mail) mail')
        ->from('table.comments')
        ->where('status = ?', 'approved')
        ->where('created > ?', $period )
        ->where('type = ?', 'comment')
        ->where('authorId = ?', '0')
        ->where('mail != ?', $options->socialemail)   //排除自己上墙
        ->group('author')
        ->order('cnt', Typecho_Db::SORT_DESC)
        ->limit('51');    //读取几位用户的信息

    $result_7days = $db->fetchAll($sql_7days);
    $html_7days = "<h4 class='font-bold m-b-lg'>本周评论排行榜</h4>";
    $echoCount = 0;
    if (count($result_7days) > 0) {
        $html_7days .= '<div class="row">';
        foreach ($result_7days as $value) {
            $specialColor = "";
            if ($echoCount <3){
                $specialColor = $color[$echoCount %3];
            }

            $avatar = Utils::getAvator($value['mail'],65);
            if (trim($value['url']) == ""){
                $guestDes = _mt("一位热心的网友路过");
            }else{
                $guestDes = $value['url'];
            }
            $html_7days .=<<<EOF
<div class="col-sm-6"> 
   <div class="panel panel-default"> 
    <div class="panel-body"> 
     <a target="_blank" rel="nofollow" href="{$value['url']}">
    <span class="pull-left thumb-sm avatar m-r"> <img src="{$avatar}" /> </span> 
     <span class="badge {$specialColor} pull-right">{$value['cnt']}</span> 
     <span class="clear text-ellipsis"> <span>{$value['author']}</span> <small class="text-muted clear 
     text-ellipsis">{$guestDes}</small> </span> 
</a>
    </div> 
   </div> 
  </div>
EOF;

            $echoCount ++;
        }
        $html_7days .= '</div>';
        echo $html_7days;
    }else{

    }
    // 一周排行榜
    $sql = $db->select('COUNT(author) AS cnt', 'author', 'max(url) url', 'max(mail) mail')
        ->from('table.comments')
        ->where('status = ?', 'approved')
        ->where('type = ?', 'comment')
        ->where('authorId = ?', '0')
        ->where('mail != ?', $options->socialemail)   //排除自己上墙
        ->group('author')
        ->order('cnt', Typecho_Db::SORT_DESC)
        ->limit('51');    //读取几位用户的信息
    $result = $db->fetchAll($sql);

    $mostactive = "<h4 class='font-bold m-b-lg m-t-lg'>总评论排行榜</h4>";
    $echoCount = 0;
    if (count($result) > 0) {
        $mostactive .= '<div class="row">';
        foreach ($result as $value) {
            $specialColor = "";
            if ($echoCount <3){
                $specialColor = $color[$echoCount %3];
            }

            $avatar = Utils::getAvator($value['mail'],65);
            if (trim($value['url']) == ""){
                $guestDes = _mt("一位热心的网友路过");
            }else{
                $guestDes = $value['url'];
            }
            $mostactive .=<<<EOF
<div class="col-sm-6"> 
   <div class="panel panel-default"> 
    <div class="panel-body"> 
    <a target="_blank" rel="nofollow" href="{$value['url']}">
    <span class="pull-left thumb-sm avatar m-r"> <img src="{$avatar}" /> </span> 
     <span class="badge {$specialColor} pull-right">{$value['cnt']}</span> 
     <span class="clear text-ellipsis"> <span>{$value['author']}</span> <small class="text-muted clear 
     text-ellipsis">{$guestDes}</small> </span> 
</a>
    
    </div> 
   </div> 
  </div>
EOF;
            $echoCount ++;

        }
        $mostactive .= '</div>';
        echo $mostactive;
    }

    //刚刚来过
    $sql_just = $db->select('COUNT(author) AS cnt', 'max(author) author', 'max(url) url', 'max(mail) mail','max(created) created')
        ->from('table.comments')
        ->where('status = ?', 'approved')
        ->where('type = ?', 'comment')
        ->where('authorId = ?', '0')
        ->where('mail != ?', $options->socialemail)   //排除自己上墙
        ->group('author')
        ->limit('50')
        ->order('created', Typecho_Db::SORT_DESC);

    $sql_just = $db->select('COUNT(author) AS cnt', 'author', 'max(url) url', 'max(mail) mail')
        ->from('table.comments')
        ->where('status = ?', 'approved')
        ->where('created > ?', $period )
        ->where('type = ?', 'comment')
        ->where('authorId = ?', '0')
        ->where('mail != ?', $options->socialemail)   //排除自己上墙
        ->group('author')
        ->order('cnt', Typecho_Db::SORT_DESC)
        ->limit('51');    //读取几位用户的信息

    $result_just = $db->fetchAll($sql_just);
    if (count($result_just) > 0){
        echo '<div class=\'font-bold m-b m-t-lg\'>这些人刚刚排队过来看过我</div>';
        $echoCount = 0;
        $justComment = '<div class="m-b m-t-lg">';
        $color = array("on","busy","away","off");
        foreach ($result_just as $value){
            if ($echoCount > 50)
                break;
            $avatar = Utils::getAvator($value['mail'],65);
            $justComment .= <<<EOF
            <a href="{$value['url']}" class="avatar thumb-xs m-r-xs m-b-xs">
          <img src="{$avatar}">
          <i class="{$color[$echoCount % 4]} b-white"></i>
        </a>
EOF;
            $echoCount ++;

        }
        $justComment .= '<a class="btn btn-success btn-rounded font-bold"> +'.count($result_just).' </a>
      </div>';

        echo $justComment;
    }

}


function themeFields(Typecho_Widget_Helper_Layout $layout){

    if (Handsome::$times <1){
        echo Handsome::outputEditorJS();
    }

    $thumbChoice = new Typecho_Widget_Helper_Form_Element_Select('thumbChoice', array(
        'default'=>'跟随外观设置',
        'yes'=> '首页文章页面均显示头图',
        'yes_only_index' => ' 仅首页显示头图',
        'yes_only_post' => ' 仅文章页面显示头图',
        'no'=> '均不显示头图'
    ),'default', _t('当前文章是否显示头图'), '<strong style="color:red;">该设置仅对该篇文章有效</strong></br>默认选项是「均显示头图」</br> 选择「均不显示头图」当前文章页面和首页将不会显示头图</br> 选择「均不显示头图」 或者「仅文章页面显示头图」，<strong style="color: red">可以继续配置「个性化标徽选择」选项</strong>');
    $layout->addItem($thumbChoice);

    $thumb = new Typecho_Widget_Helper_Form_Element_Text('thumb', NULL, "", _t('大头图地址'), _t('输入图片URL，则优先使用该图片作为头图</br>不填此处则按照后台外观设置中的 <code>主题增强功能-博客头图设置</code>顺序显示头图。</br>大头图的尺寸为8：3'));
    $layout->addItem($thumb);
    $thumbSmall = new Typecho_Widget_Helper_Form_Element_Text('thumbSmall', NULL, "", _t('小头图地址'), _t('因为当首页文章显示小头图的时候，文章页面仍然会显示大头图</br>当前文章如果不是小头图样式，你可以忽略该设置项。</br>如果当前文章是小头图你仍然也可以不填，默认会自动大头图的图片至正方形尺寸显示在首页</br>但是为了最佳体验，你可以填一张小图片（正方形尺寸）'));
    $layout->addItem($thumbSmall);

    $thumbStyle = new Typecho_Widget_Helper_Form_Element_Select('thumbStyle', array(
        'default'=>'跟随外观设置',
        'large'=>_mt('大版式'),
        'small'=>'小版式'
    ),'default', _t('文章头图样式选择'), '该选项可以单独为该篇文章配置头图样式，以便达到首页多种头图样式交叉的效果');
    $layout->addItem($thumbStyle);

    //选择无头图样式
    $noThumbInfoStyle = new Typecho_Widget_Helper_Form_Element_Select('noThumbInfoStyle', array(
        'default'=>'无',
        'book'=>_mt('图书'),
        'game'=>'游戏',
        'note' => '笔记',
        'chat' => '聊天',
        'code' => '代码',
        'image' => '图片',
        'web' => '网页',
        'link' => '链接',
        'design' => '设计',
        'lock' => '上锁'
    ),'default', _t('个性化标徽选择'), '该选项仅在<strong style="color: red">首页当前文章不显示头图</strong>，样式才会有效</br> 点击<a href="https://s2.ax1x.com/2019/07/20/ZzatnU.jpg">这里依次预览所有图标</a>');
    $layout->addItem($noThumbInfoStyle);

    $outdatedNotice= new Typecho_Widget_Helper_Form_Element_Select('outdatedNotice', array(
        'no'=>'关闭',
        'yes'=>_mt('开启')
    ),'no', _t('开启文章过时提醒'), '当该文章的最后更新时间距离游客访问时间超过60天，会在文章顶部显示一条“文章可能过时”的提醒</br>默认关闭，你可以在这里开启，仅对该篇文章有效');
    $layout->addItem($outdatedNotice);

    $customSummary = new Typecho_Widget_Helper_Form_Element_Text("customSummary",null,"","手动指定摘要内容","默认根据后台配置的摘要字数<a target='_blank' href='https://handsome.ihewro.com/#/functions?id=%E8%87%AA%E5%AE%9A%E4%B9%89%E6%91%98%E8%A6%81%E5%AD%97%E6%95%B0'>自动生成摘要</a></br><strong style='color: red'>你可以在这里手动指定摘要</strong>");
    $layout->addItem($customSummary);

}
