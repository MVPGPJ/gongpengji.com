<?php
/**
 * 世上每个人都是被上帝咬过一口的苹果，都是有缺陷的人。有的人缺陷比较大，是因为上帝特别喜爱他的芬芳。
 * @package handsome
 * @author gpj
 * @version 5.2.0
 * @link https://www.ihewro.com/archives/489/
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
 $this->need('component/header.php');
 ?>

  <!-- aside -->
  <?php $this->need('component/aside.php'); ?>
  <!-- / aside -->

<!-- <div id="content" class="app-content"> -->
  <a class="off-screen-toggle hide"></a>
  <main class="app-content-body <?php Content::returnPageAnimateClass($this); ?>">
    <div class="hbox hbox-auto-xs hbox-auto-sm">
      <div class="col center-part">
          <?php if($this->options->blogNotice): ?>
              <!--公告位置-->
          <div class="alert alert-warning alert-block" style="
              margin-bottom: 0px;">
              <button type="button" class="close" data-dismiss="alert">×</button><p><i class="fontello fontello-volume-up" aria-hidden="true"></i>&nbsp;
                  <?php $this->options->blogNotice(); ?></p>
          </div>
              <!--/公告位置-->
          <?php endif; ?>
        <header class="bg-light lter b-b wrapper-md">
          <h1 class="m-n font-thin h3 text-black l-h"><?php $this->options->title(); ?></h1>
          <small class="text-muted letterspacing indexWords"><?php
              if (@!in_array('hitokoto',$this->options->featuresetup)) {
                  $this->options->Indexwords();
              }else{
                  echo '加载中……';
                  echo '<script>
                         $.ajax({
                            type: \'Get\',
                            url: \'https://v1.hitokoto.cn/\',
                            success: function(data) {
                               var hitokoto = data.hitokoto;
                              $(\'.indexWords\').text(hitokoto);
                            }
                         });
</script>';
              }
              ?></small>
          </header>
        <div class="wrapper-md" id="post-panel">
            <!--首页输出文章-->

            <?php Content::echoPostList($this) ?>
          <!--分页首页按钮-->
          <nav class="text-center m-t-lg m-b-lg" role="navigation">
        <?php $this->pageNav('<i class="fontello fontello-chevron-left"></i>', '<i class="fontello fontello-chevron-right"></i>'); ?>
          </nav>
            <style>
                .page-navigator>li>a, .page-navigator>li>span{
                    line-height: 1.42857143;
                    padding: 6px 12px;
                }
            </style>
        </div>
      </div>
      <!--首页右侧栏-->
      <?php $this->need('component/sidebar.php') ?>
    </div>
  </main>
    <!-- footer -->
  <?php $this->need('component/footer.php'); ?>
    <!-- / footer -->
