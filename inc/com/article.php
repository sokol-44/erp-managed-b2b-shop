<?php
$Article = new Article($F->GET['key']);

if( !$Article ) {
   $F->redirect( $F->make_link(CNF_DEFAULT_PAGE));
}

$BC->add_crumb( array( 'name' => $Article->param['title'], 'path' => $F->make_link(CFG_COM_ARTICLE) ) );

$Page->head_title = $Article->param['title'];
?>
<div class="article_container">
  <div class="article_container article_title container_header"><?php echo $Article->param['title']; ?><div class="icon"></div></div>
  <div class="article_container article_content"><?php echo $Article->param['content']; ?></div>
  <div class="article_container article_bottom container_bottom"></div>
</div>