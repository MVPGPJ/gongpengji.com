<?php
/**
 * User: hewro
 * Create: 2018/7/17
 * Time: 20:43
 * 一些ajax请求，能够有效的提升用户体验
 */


/**
 * @param $content
 * @param $rootUrl
 * @return string
 */
function typeLocationContent($content,$rootUrl){
    $locations = mb_split('#',$content);
    $label = $locations[2];
    $imageUrl = $locations[3];
    //这里的content是url地址
    $url = Utils::uploadPic($rootUrl,uniqid(),$imageUrl,"web",".jpg");
    $content = '📌'.$label.'<img src="'.$url.'"/>';
    return $content;
}

function typeImageContent($content,$rootUrl){
    $url = Utils::uploadPic($rootUrl,uniqid(),$content,"web",".jpg");
    $content = '<img src="'.$url.'"/>';
    return $content;
}

function typeTextContent($content,$flag = true){
    if ($flag){
        $content = $content."</br>";
    }
    return $content;
}

function typeLinkContent($content){
    $links = mb_split('#',$content);
    $title = $links[0];
    $description = $links[1];
    $url = $links[2];
    //对url进行转义
    $url = str_replace('','\/',$url);
    $content = '[post title="'.$title.'" intro="'.$description.'" url="'.$url.'" /]';
    return $content;
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if(@$_POST['action'] == 'send_talk'){//从微信公众号发送说说说
        //获取必要的参数
        if (!empty($_POST['content']) && !empty($_POST['time_code']) && !empty($_POST['cid']) && !empty($_POST['token'])){
            $cid = $_POST['cid'];
            $content=$_POST['content']; //发送的内容
            $time_code= $_POST['time_code'];//用来检验是否是博客主人
            $token= $_POST['token'];//用来表示调用这个接口的来源，wexin表示微信公众号，crx表示浏览器扩展
            $msg_type = $_POST['msg_type'];
            $options = mget();

            //身份验证
            if ($time_code == md5($options->time_code)){//验证成功
                $isHaveImage = false;
                $imageContent = "[album]";
                if ($msg_type == "image"){//上传图片
                    $content = typeImageContent($content,$options->rootUrl);
                }else if ($msg_type == "location"){//地理位置
                    $content = typeLocationContent($content,$options->rootUrl);
                }else if($msg_type == "mixed"){//混合类型，content是json字符串，需要解析成数组
                    $contentArray = json_decode($content,true);
                    $contentArray = $contentArray["results"];
                    $content = "";
                    //对图片进行上传
                    foreach ($contentArray as $contentItem){
                        if ($contentItem['type'] == "image"){
                            $isHaveImage = true;
                            $imageContent .= typeImageContent($contentItem['content'],$options->rootUrl);
                        }elseif ($contentItem['type'] == "text"){
                            $content .= typeTextContent($contentItem['content'],true);
                        }elseif ($contentItem['type'] == "location"){
                            $content .= typeLocationContent($contentItem['content'],$options->rootUrl);
                        }else if ($contentItem['type'] == "link"){
                            $content = typeLinkContent($contentItem['content']);
                        }
                    }
                    if ($isHaveImage){
                        $imageContent .= "[/album]";
                        $content .= typeTextContent($imageContent,false);
                    }
                }else if ($msg_type == "text"){
                    $content = typeTextContent($content,false);
                }else if ($msg_type == "link"){
                    $content = typeLinkContent($content);
                }

                //向数据库添加说说记录
                $db = Typecho_Db::get();
                //先找到作者信息
                $getAdminSql = $db->select()->from('table.users')
                    ->limit(1);
                $user = $db->fetchRow($getAdminSql);

                $insert = $db->insert('table.comments')
                    ->rows(array("cid" => $cid,"created" => time(),"author" => $user['screenName'],"authorId" =>
                        $user['uid'],"ownerId" => $user['uid'],"text"=> $content,"url" => $user['url'],"mail" =>
                        $user['mail'],"agent"=>$token));
                //将构建好的sql执行, 如果你的主键id是自增型的还会返回insert id
                $insertId = $db->query($insert);
                //修改评论数目+1
                $row = $db->fetchRow($db->select('commentsNum')->from('table.contents')->where('cid = ?',$cid));
                $db->query($db->update('table.contents')->rows(array('commentsNum' => (int) $row['commentsNum'] + 1))->where('cid = ?', $cid));
                echo "1";
            }else{
                echo "-3";//身份验证失败
            }

        }else{
            echo "-2";//信息缺失
        }
        die();
    }
    else if(@$_POST['action'] == 'upload_img'){
        $returnData = array();
        //支持上传base64数据和url格式两种，网络图片一律使用.jpg格式
        $options = mget();
        //鉴权：判断是否登录或者根据时光机id来判断
        $flag = false;//验证通过
        if ($this->user->hasLogin()){
            $flag = true;
        }elseif ($_POST['time_code'] == md5($options->time_code)){
            $flag = true;
        }else{
            $flag = false;
        }
        if ($flag){
            $data = $_POST['file'];
            $prefix = substr($data,0,4);
            if ($prefix == "data"){//本地图片
                $base64_string= explode(',', $data); //截取data:image/png;base64, 这个逗号后的字符
//                根据数据自动识别不需要传递这个参数了
//                这种方法获取的固定是png 不知道为什么？？
                @$suffix = ".".explode(";",explode("/",$base64_string[0])[1])[0];
                $data= base64_decode($base64_string[1]);
                $returnData['status'] = "1";
                $returnData['data'] = Utils::uploadPic($options->rootUrl,uniqid(),$data,"local",$suffix);
            }else if ($prefix == "http"){//网络图片
                $returnData['status'] = "1";
                $returnData['data'] = Utils::uploadPic($options->rootUrl,uniqid(),$data,"web",".jpg");
            }else{
                $returnData['status'] = "-1";//请求参数错误
            }
        }else{
            $returnData['status'] = "-3";//身份验证错误
        }
        //用json字符串格式返回请求信息
        echo json_encode($returnData);
        die();
    }elseif(@$_POST['action'] == 'notice2'){
        $data = $_POST['data'];

        $data = json_decode($data,true);
        $code = $_POST['code'];
        $options = mget();
        if (md5($options->time_code) == $code){
            //只有登录状态才可以调用该接口
            if ($data['action'] == "0"){
                $data = Handsome_Config::god;
            }else {
                $data = Handsome_Config::back_god;
            }
            $path = Handsome_Config::returnPath();
            @$fp = fopen($path, "w");
            if ($fp){
                fwrite($fp, $data);
                echo "1";
                fclose($fp);
            }else{
                echo "-2";
            }

        }else{
            echo  "-1";
        }

        die();
    }
}else if ($_SERVER["REQUEST_METHOD"] == "GET"){
    if(@$_GET['action'] == 'ajax_avatar_get') {
        $email = strtolower( $_GET['email']);
        echo Utils::getAvator($email,65);
        die();
    }elseif(@$_GET['action'] == 'send_talk'){
        echo "非法get请求";
        die();
    }else if (@$_GET['action'] == 'star_talk'){
        if (!empty($_GET['coid'])){
            $coid = $_GET['coid'];
            $db = Typecho_Db::get();

            $stars = Typecho_Cookie::get('extend_say_stars');
            if(empty($stars)){
                $stars = array();
            }else{
                $stars = explode(',', $stars);
            }
            $row = $db->fetchRow($db->select('stars')->from('table.comments')->where('coid = ?',$coid));

            if(!in_array($coid,$stars)){//如果cookie不存在才会加1
                $db->query($db->update('table.comments')->rows(array('stars' => (int) $row['stars'] + 1))->where('coid = ?', $coid));
                array_push($stars, $coid);
                $stars = implode(',', $stars);
                Typecho_Cookie::set('extend_say_stars', $stars); //记录查看cookie
                echo 1;//点赞成功
            }else{
                echo 2;//已经点赞过了
            }
        }else{
            echo -1;//信息缺失
        }

        die();
    }
    else if(@$_GET['action'] == 'open_world'){
        if (!empty($_GET['password'])){
            $password = $_GET['password'];
            $md5 = $_GET['md5'];
            $type = $_GET['type'];//type:index 表示首页 category 表示分类加锁
            $options = mget();
            if (md5($password) == $md5){
                echo 1;//密码正确
                if ($type == "index"){
                    Typecho_Cookie::set('open_new_world', md5($password)); //保存密码的cookie，以便后面可以直接访问
                }elseif($type == "category") {
                    $category = $_GET['category'];//需要加密的分类缩略名
                    Typecho_Cookie::set('category_'.$category, md5($password)); //保存密码的cookie，以便后面可以直接访问
                }
            }else{
                echo -1;//密码错误
            }
        }else{
            echo -2;//信息不完成
        }

        die();
    }
    else if (@$_GET['action'] == 'back_up' || @$_GET['action'] == 'un_back_up' || @$_GET['action'] == 'recover_back_up'){//备份管理

        $action = $_GET['action'];
        $db = Typecho_Db::get();

        $themeName = $db->fetchRow($db->select()->from ('table.options')->where ('name = ?', 'theme'));
        $handsomeThemeName = "theme:".$themeName['value'];
        $handsomeThemeBackupName = "theme:HandsomePro-X-Backup";


        if ($action == "back_up"){//备份数据
            $handsomeInfo=$db->fetchRow($db->select()->from ('table.options')->where ('name = ?', $handsomeThemeName));
            $handsomeValue = $handsomeInfo['value'];//最新的主题数据

            if($db->fetchRow($db->select()->from ('table.options')->where ('name = ?', $handsomeThemeBackupName))) {//如果有了，直接更新
                $update = $db->update('table.options')->rows(array('value' => $handsomeValue))->where('name = ?', $handsomeThemeBackupName);
                $updateRows = $db->query($update);
                echo 1;
            }else{//没有的话，直接插入数据
                $insert = $db->insert('table.options')
                    ->rows(array('name' => $handsomeThemeBackupName,'user' => '0','value' => $handsomeValue));
                $db->query($insert);
                echo 2;
            }
        }else if ($action == "un_back_up"){//删除备份
            $db = Typecho_Db::get();
            if($db->fetchRow($db->select()->from ('table.options')->where ('name = ?', $handsomeThemeBackupName))){
                $delete = $db->delete('table.options')->where ('name = ?', $handsomeThemeBackupName);
                $deletedRows = $db->query($delete);
                echo 1;
            }else{
                echo -1;//备份不存在
            }
        }else if ($action == "recover_back_up"){//恢复备份
            $db = Typecho_Db::get();
            if($db->fetchRow($db->select()->from ('table.options')->where ('name = ?', $handsomeThemeBackupName))){
                $themeInfo = $db->fetchRow($db->select()->from ('table.options')->where ('name = ?',
                    $handsomeThemeBackupName));
                $themeValue = $themeInfo['value'];
                $update = $db->update('table.options')->rows(array('value'=>$themeValue))->where('name = ?', $handsomeThemeName);
                $updateRows= $db->query($update);
                echo 1;
            }else{
                echo -1;//没有备份数据
            }
        }
        die();//只显示ajax请求内容，禁止显示博客内容
    }else if (@$_GET['action'] == "ajax_search"){
        $content = @$_GET['content'];
        $OnlyTitle = @$_GET['onlytitle'];//只查询标题字段
        if (trim($content)!=""){
            $db = Typecho_Db::get();
            $searchQuery = '%' . str_replace(' ', '%', $content) . '%';
            $sql = $db->select()->from('table.contents')
                ->join('table.relationships','table.relationships.cid = table.contents.cid','right')->join('table.metas','table.relationships.mid = table.metas.mid','right')->where('table.metas.type=?','category')
                ->where("table.contents.password IS NULL OR table.contents.password = ''")
                ->where('table.contents.title LIKE ? OR table.contents.text LIKE ?', $searchQuery, $searchQuery)
                ->where('table.contents.type = ?', 'post')
                ->order('table.contents.created', Typecho_Db::SORT_DESC)
                ->limit(10);
            $result = $db->fetchAll($sql);//查看评论中是否有该游客的信息
            if (count($result) == 0){
                $result = array();
                $res = new stdClass();
                $res->title = "暂无可提供的搜索结果";
                $res->click = "0";
                $result[0] = $res;
            }
            echo json_encode($result);
        }else{
            echo json_decode("");
        }
        die();
    } else {//无需action标识，全站加密
        $options = mget();
        //如果路径包含后台管理路径，则不加密
        $password = Typecho_Cookie::get('open_new_world');
        $cookie = false;//true为可以直接进入
        if (!empty($password) && $password == md5($options->open_new_world)){
            $cookie = true;
        }

        if (!$cookie && trim($options->open_new_world) != "" && !strpos($_SERVER["SCRIPT_NAME"],
                __TYPECHO_ADMIN_DIR__)){//没有cookie认证且访问的不是管理员界面
            $data = array();
            $data['title'] = $this->options->title;
            $data['md5'] = md5($options->open_new_world);
            $data['type'] = "index";
            $data['category'] = "";
            $_GET['data']=$data;
            require_once('Lock.php');
            die();
        }else{
            //检查网站前端服务器环境是否有必要的函数是否支持以及保证必要的读写功能以便主题正常运行
            if (!strpos($_SERVER["SCRIPT_NAME"],
                __TYPECHO_ADMIN_DIR__)){
                /*if (!function_exists("mb_split") || !function_exists("file_get_contents")){
                throw new Typecho_Exception(_t(Handsome_Config::not_support));
            }*/
                $path = Handsome_Config::returnPath();
                $flag = true;
                if (!file_exists($path)){
                    $flag = false;
                }else{

                    if (Handsome_Config::god != file_get_contents($path)){
                        $flag = false;
                    }
                }
                if (!$flag){
                    throw new Typecho_Exception(_t(Handsome_Config::bad));
                }
                return;
            }

        }
    }
}
