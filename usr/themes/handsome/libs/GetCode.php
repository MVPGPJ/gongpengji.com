<?php
    /**
     * @author hewro
     * @description 生成网站二维码的接口
     */

require_once './phpqrcode.php';


if ($_SERVER["REQUEST_METHOD"] == "GET"){



    if (!empty($_GET['type']) && !empty($_GET['content'])){
        $type = $_GET['type'];
        $content = $_GET['content'];
        getImageCode($content,$type);
    }

}


function getImageCode($content,$type){
    $value = $content;                  //二维码内容
    $errorCorrectionLevel = 'L';    //容错级别
    $matrixPointSize = 5;           //生成图片大小
    //生成二维码图片
    QRcode::png($value,false,$errorCorrectionLevel, $matrixPointSize, 2);

}


?>