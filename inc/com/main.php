<?php


if( $P->logged_in ) $F->redirect( $F->make_link(CFG_COM_ACCOUNT) );
$F->redirect( $F->make_link(CFG_COM_LOGIN));
die();

$Article = new Article('1');

if( !$Article ) {
   $F->redirect( $F->make_link(CNF_DEFAULT_COM));
}

$BC->add_crumb( array( 'name' => Lang::_('MAIN_PAGE'), 'path' => $F->make_link(CFG_COM_ARTICLE) ) );
   
$Page->head_title = $Article->param['title'];
?>

<div class="article_container">
  <div class="article_container article_title container_header"><?php echo $Article->param['title']; ?><div class="icon"></div></div>
  <div class="article_container article_content"><?php echo $Article->param['content']; ?></div>
  <div class="article_container article_bottom container_bottom"></div>
</div>
