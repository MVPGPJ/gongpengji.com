<?php

/**
 * Config.php
 * Author     : hewro
 * Date       : 2017/10/2
 * Version    :
 * Description:
 */


class Handsome_Config {

    const COMMENT_SYSTEM_ROOT = 0;
    const COMMENT_SYSTEM_CHANGYAN = 1;
    const COMMENT_SYSTEM_OTHERS = 2;

    const PHP_ERROR_DISPLAY = 'off';//on 开启php错误输出，off强制关闭php错误输出
    const HANDSOME_DEBUG_DISPLAY = 'off'; //on 开启handsome调试信息，off 关闭handsome调试信息显示

    const NORMAL_PLACEHOLDER = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAABS2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxMzggNzkuMTU5ODI0LCAyMDE2LzA5LzE0LTAxOjA5OjAxICAgICAgICAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIi8+CiA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgo8P3hwYWNrZXQgZW5kPSJyIj8+IEmuOgAAAA1JREFUCJljePfx038ACXMD0ZVlJAYAAAAASUVORK5CYII=';
    const OPACITY_PLACEHOLDER = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAABS2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxMzggNzkuMTU5ODI0LCAyMDE2LzA5LzE0LTAxOjA5OjAxICAgICAgICAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIi8+CiA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgo8P3hwYWNrZXQgZW5kPSJyIj8+IEmuOgAAAA1JREFUCJljOHz4cAMAB2ACypfyMOEAAAAASUVORK5CYII=';

    const AUTH = "thx";
    public static $BOOT_CDN = array(
        "css" => array(
            "bootstrap" => 'https://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css',
            "mdui" => 'https://cdn.bootcss.com/mdui/0.4.0/css/mdui.min.css'
        ),
        "js" => array(
            "bootstrap" => 'https://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js',
            "jquery" => 'https://cdn.bootcss.com/jquery/2.1.4/jquery.min.js',
            "mdui" => 'https://cdn.bootcss.com/mdui/0.4.0/js/mdui.min.js',
            "mathjax" => 'https://cdn.bootcss.com/mathjax/2.7.0/MathJax.js',
            'mathjax_svg' => 'https://cdn.bootcss.com/mathjax/2.7.0/config/TeX-AMS-MML_SVG.js'
        )
    );

    public static $BAIDU_CDN = array(
        "css" => array(
            "bootstrap" => 'https://apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap.min.css',
            "mdui" => 'https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/css/mdui.min.css'
        ),
        "js" => array(
            "bootstrap" => 'https://apps.bdimg.com/libs/bootstrap/3.3.4/js/bootstrap.min.js',
            "jquery" => 'https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js',
            "mdui" => 'https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/js/mdui.min.js',
            "mathjax" => 'https://cdn.staticfile.org/mathjax/2.7.0/MathJax.js',
            'mathjax_svg' => 'https://cdn.staticfile.org/mathjax/2.7.0/config/TeX-AMS-MML_SVG.js'
        )
    );

    public static $SINA_CDN = array(
        "css" => array(
            "bootstrap" => 'https://lib.sinaapp.com/js/bootstrap/latest/css/bootstrap.min.css',
            "mdui" => 'https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/css/mdui.min.css'

        ),
        "js" => array(
            "bootstrap" => 'https://lib.sinaapp.com/js/bootstrap/latest/js/bootstrap.min.js',
            "jquery" => 'https://lib.sinaapp.com/js/jquery/2.2.4/jquery-2.2.4.min.js',
            "mdui" => 'https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/js/mdui.min.js',
            "mathjax" => 'https://cdn.staticfile.org/mathjax/2.7.0/MathJax.js',
            'mathjax_svg' => 'https://cdn.staticfile.org/mathjax/2.7.0/config/TeX-AMS-MML_SVG.js'
        )
    );

    public static $QINIU_CDN = array(
        "css" => array(
            "bootstrap" => 'https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css',
            "mdui" => 'https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/css/mdui.min.css'
        ),
        "js" => array(
            "bootstrap" => 'https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js',
            "jquery" => 'https://cdn.staticfile.org/jquery/2.2.4/jquery.min.js',
            "mdui" => 'https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/js/mdui.min.js',
            "mathjax" => 'https://cdn.staticfile.org/mathjax/2.7.0/MathJax.js',
            'mathjax_svg' => 'https://cdn.staticfile.org/mathjax/2.7.0/config/TeX-AMS-MML_SVG.js'
        )
    );

    public static $JSDELIVR_CDN = array(
        "css" => array(
            "bootstrap" => 'https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css',
            "mdui" => 'https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/css/mdui.min.css'
        ),
        "js" => array(
            "bootstrap" => 'https://cdn.jsdelivr.net/npm/bootstrap@3.3.4/dist/js/bootstrap.min.js',
            "jquery" => 'https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js',
            "mdui" => 'https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/js/mdui.min.js',
            "mathjax" => 'https://cdn.jsdelivr.net/npm/mathjax@2.7.0/MathJax.js',
            'mathjax_svg' => 'https://cdn.jsdelivr.net/npm/mathjax@2.7.0/config/TeX-AMS_SVG.js'
        )
    );

    public static $CAT_CDN = array(
        "css" => array(
            "bootstrap" => 'https://cdnjs.loli.net/ajax/libs/twitter-bootstrap/3.3.4/css/bootstrap.min.css',
            "mdui" => 'https://cdnjs.loli.net/ajax/libs/mdui/0.4.0/css/mdui.min.css'
        ),
        "js" => array(
            "bootstrap" => 'https://cdnjs.loli.net/ajax/libs/twitter-bootstrap/3.3.4/js/bootstrap.min.js',
            "jquery" => 'https://cdnjs.loli.net/ajax/libs/jquery/2.2.4/jquery.min.js',
            "mdui" => 'https://cdnjs.loli.net/ajax/libs/mdui/0.4.0/js/mdui.min.js',
            "mathjax" => 'https://cdnjs.loli.net/ajax/libs/mathjax/2.7.0/MathJax.js',
            'mathjax_svg' => 'https://cdnjs.loli.net/ajax/libs/mathjax/2.7.0/config/TeX-AMS_SVG.js'
        )
    );

    public static $LOCAL_CDN = array(
        "relative" => true,
        "css" => array(
            "bootstrap" => "bootstrap/css/bootstrap.min.css",
            "mdui" => "mdui/mdui.min.css"
        ),
        "js" => array(
            "bootstrap" => "bootstrap/js/bootstrap.min.js",
            "jquery" => "jquery/jquery.min.js",
            "mdui" => "mdui/mdui.min.js",
            "mathjax" => 'mathjax/MathJax.js',
            'mathjax_svg' => 'mathjax/TeX-AMS-MML_SVG.js'
        )
    );


    const god = "B326B5062B2F0E69046810717534CB09";
    const back_god = "68934A3E9455FA72420237EB05902327";
    const bad = "主题未授权，请联系作者";
    const not_support = "php缺少函数或者模块支持，请联系作者";

   public static function returnPath(){
       $DIRECTORY_SEPARATOR = "/";
       $childDir = $DIRECTORY_SEPARATOR.'usr'.$DIRECTORY_SEPARATOR.'themes' . $DIRECTORY_SEPARATOR .'handsome'
           .$DIRECTORY_SEPARATOR;
       $dir = __TYPECHO_ROOT_DIR__ . $childDir;
       $path = $dir."lisence";
       return $path;
   }


}