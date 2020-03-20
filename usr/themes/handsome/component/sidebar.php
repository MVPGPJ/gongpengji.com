  <?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
  <?php if (@!in_array('all',$this->options->sidebarSetting)): ?>
     <aside class="asideBar col w-md bg-white-only b-l bg-auto no-border-xs" role="complementary">
     <div id="sidebar">
      <section id="tabs-4" class="widget widget_tabs clear">
       <div class="nav-tabs-alt no-js-hide">
        <ul class="nav nav-tabs nav-justified" role="tablist">
         <li class="active" role="presentation"> <a href="#widget-tabs-4-hots" role="tab" aria-controls="widget-tabs-4-hots" aria-expanded="true" data-toggle="tab"> <i class="glyphicon glyphicon-fire text-md text-muted wrapper-sm" aria-hidden="true"></i> <span class="sr-only"><?php _me("热门文章") ?></span> </a></li>
            <?php if (COMMENT_SYSTEM == 0): ?>
         <li role="presentation"> <a href="#widget-tabs-4-comments" role="tab" aria-controls="widget-tabs-4-comments" aria-expanded="false" data-toggle="tab"> <i class="glyphicon glyphicon-comment text-md text-muted wrapper-sm" aria-hidden="true"></i> <span class="sr-only"><?php _me("最新评论") ?></span> </a></li>
            <?php endif; ?>
         <li role="presentation"> <a href="#widget-tabs-4-random" role="tab" aria-controls="widget-tabs-4-random" aria-expanded="false" data-toggle="tab"> <i class="glyphicon glyphicon-transfer text-md text-muted wrapper-sm" aria-hidden="true"></i> <span class="sr-only"><?php _me("随机文章") ?></span> </a></li>
        </ul>
       </div>
       <div class="tab-content">
       <!--热门文章-->
        <div id="widget-tabs-4-hots" class="tab-pane  fade in wrapper-md active" role="tabpanel">
         <h3 class="widget-title m-t-none text-md"><?php _me("热门文章") ?></h3>
         <ul class="list-group no-bg no-borders pull-in m-b-none">
          <?php Content::returnHotPosts($this); ?>
         </ul>
        </div>
           <?php if (COMMENT_SYSTEM == 0): ?>
        <!--最新评论-->
        <div id="widget-tabs-4-comments" class="tab-pane fade wrapper-md no-js-show" role="tabpanel">
         <h3 class="widget-title m-t-none text-md"><?php _me("最新评论") ?></h3>
         <ul class="list-group no-borders pull-in auto m-b-none no-bg">
          <?php $this->widget('Widget_Comments_Recent', 'ignoreAuthor=true&pageSize=5')->to($comments); ?>
          <?php while($comments->next()): ?>
          <li class="list-group-item">

              <a href="<?php $comments->permalink(); ?>" class="pull-left thumb-sm avatar m-r">
                  <?php
                      if (count($this->options->indexsetup)>0 && !in_array('notShowRightSideThumb',$this->options->indexsetup)){
                          echo Utils::avatarHtml($comments);
                      }
                  ?>
              </a>
              <a href="<?php $comments->permalink(); ?>" class="text-muted">
                  <!--<i class="iconfont icon-comments-o text-muted pull-right m-t-sm text-sm" title="<?php /*_me("详情") */?>" aria-hidden="true" data-toggle="tooltip" data-placement="auto left"></i>
                  <span class="sr-only"><?php /*_me("评论详情") */?></span>-->
              </a>
              <div class="clear">
                  <div class="text-ellipsis">
                      <a href="<?php $comments->permalink(); ?>" title="<?php $comments->author(false); ?>"> <?php $comments->author(false); ?> </a>
                  </div>
                  <small class="text-muted">
                      <span>
                          <?php
                              $content = Content::postCommentContent(Markdown::convert($comments->text),
                              $this->user->hasLogin(),"","","");
                              $commentValue = $content;
                              $commentValue = strip_tags($commentValue);
                              $commentValue = trim($commentValue);
                              if ($commentValue == "") {//只含有空白或者空格自字符
                                  echo _mt("空白占位符");
                              } else {
                                  echo Typecho_Common::subStr($commentValue, 0, 34, "...");
                              }
                          ?>
                      </span>
                  </small>
              </div>
          </li>
          <?php endwhile; ?>
         </ul>
        </div>
           <?php endif; ?>
        <!--随机文章-->
        <div id="widget-tabs-4-random" class="tab-pane fade wrapper-md no-js-show" role="tabpanel">
            <h3 class="widget-title m-t-none text-md"><?php _me("随机文章") ?></h3>
            <ul class="list-group no-bg no-borders pull-in">
            <?php Content::returnRandomPosts($this);?>
            </ul>
        </div>
       </div>
      </section>
      <!--博客信息-->
         <?php if (@!in_array('info',$this->options->sidebarSetting)): ?>
      <section id="categories-2" class="widget widget_categories wrapper-md clear">
       <h3 class="widget-title m-t-none text-md"><?php _me("博客信息") ?></h3>
       <ul class="list-group">
           <?php Typecho_Widget::widget('Widget_Stat')->to($stat); ?>
           <li class="list-group-item"> <i class="glyphicon glyphicon-file text-muted"></i> <span class="badge
           pull-right"><?php $stat->publishedPostsNum() ?></span><?php _me("文章数目") ?></li>
           <li class="list-group-item"> <i class="glyphicon glyphicon-comment text-muted"></i> <span class="badge
           pull-right"><?php $stat->publishedCommentsNum() ?></span><?php _me("评论数目") ?></li>
           <li class="list-group-item"> <i class="glyphicon glyphicon-equalizer text-muted"></i> <span class="badge
           pull-right"><?php echo Utils::getOpenDays(); ?></span><?php _me("运行天数") ?></li>
           <li class="list-group-item"> <i class="glyphicon glyphicon-refresh text-muted"></i> <span class="badge
           pull-right"><?php echo Utils::getLatestTime($this); ?></span><?php _me("最后活动") ?></li>
       </ul>
      </section>
      <?php endif; ?>
         <?php if ($this->options->adContentSidebar != ""): ?>
         <!--广告位置-->
         <section id="a_d_sidebar" class="widget widget_categories wrapper-md clear">
             <h3 class="widget-title m-t-none text-md"><?php _me("广告") ?></h3>
            <?php $this->options->adContentSidebar(); ?>
         </section>
         <?php endif; ?>
         <!--在文章页面输出目录，在其他页面输出标签云-->
      <?php if (!($this->is('post'))) : ?>
      <section id="tag_cloud-2" class="widget widget_tag_cloud wrapper-md clear">
       <h3 class="widget-title m-t-none text-md"><?php _me("标签云") ?></h3>
       <div class="tags l-h-2x">
       <?php Typecho_Widget::widget('Widget_Metas_Tag_Cloud','ignoreZeroCount=1&limit=30')->to($tags); ?>
        <?php if($tags->have()): ?>
            <?php while ($tags->next()): ?>
            <a href="<?php $tags->permalink();?>" class="label badge" title="<?php echo sprintf(_mt("该标签下有 %d 篇文章"),
                $tags->count); ?>" data-toggle="tooltip"><?php $tags->name(); ?></a>
            <?php endwhile; ?>
        <?php endif; ?>
       </div>
      </section>
    <?php else: ?>
          <?php if (IS_TOC): ?>
          <div id="tag_toc_body">
              <section id="tag_toc" class="widget widget_categories wrapper-md clear">
                  <h3 class="widget-title m-t-none text-md"><?php _me("文章目录") ?></h3>
                  <div class="tags l-h-2x">
                      <div id="toc"></div>
                  </div>
              </section>

              <div class="hidden-lg tocify-mobile-panel panel panel-default
              setting_body_panel"
                   aria-hidden="true">
                  <button class="btn btn-default no-shadow pos-abt" data-toggle="tooltip" data-placement="left" data-original-title="<?php _me("目录") ?>" data-toggle-class=".tocify-mobile-panel=active">
                      <i class="glyphicon glyphicon-resize-full"></i>
                  </button>
                  <div class="panel-heading"><?php _me("文章目录") ?></div>
                  <div class="setting_body toc-mobile-body">
                      <div class="panel-body">
                          <div class="tocTree"></div>
                      </div>
                  </div>
              </div>
          </div>
          <?php else: ?>
              <section id="tag_cloud-2" class="widget widget_tag_cloud wrapper-md clear">
                  <h3 class="widget-title m-t-none text-md"><?php _me("标签云") ?></h3>
                  <div class="tags l-h-2x">
                      <?php Typecho_Widget::widget('Widget_Metas_Tag_Cloud','ignoreZeroCount=1&limit=30')->to($tags); ?>
                      <?php if($tags->have()): ?>
                          <?php while ($tags->next()): ?>
                              <a href="<?php $tags->permalink();?>" class="label badge" title="<?php $tags->name(); ?>" data-toggle="tooltip"><?php $tags->name(); ?></a>
                          <?php endwhile; ?>
                      <?php endif; ?>
                  </div>
              </section>
              <?php endif; ?>
    <?php endif; ?>
    </div>
     </aside>
  <?php endif; ?>