<?php
$Article = new Article('1');

if( !$Article ) {
   $F->redirect( $F->make_link(CNF_DEFAULT_PAGE));
}

$BC->add_crumb( array( 'name' => Lang::_('MAIN_PAGE'), 'path' => $F->make_link(CFG_COM_ARTICLE) ) );
   
$Page->head_title = $Article->param['title'];
?>
<div class="article_container">
  <div class="title_container"><?php echo $Article->param['title']; ?></div>
  <div class="article_content"><?php echo $Article->param['content']; ?></div>
</div>