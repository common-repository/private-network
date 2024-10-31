<?php if ($pageposts): ?>

<div class="pn-wrap">

<?php global $post; foreach ($pageposts as $post): ?>
<?php setup_postdata($post); ?>

<div class="pn-post">
  <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
  <small><?php the_time('F jS, Y') ?> by <?php the_author() ?></small>
  
  <div class="entry">
	<?php the_content('Read the rest of this entry &raquo;'); ?></div><?php /* Placing this 
																			closing div on a new line will make WP add a redundant closing
																			paragraph tag "</p>", in between the end of "the_content" ouput
																			and the closing div, that breaks XHTML
																			*/ 
																			?>
  
  <p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
</div>

<?php endforeach; ?>

</div>

<?php endif; ?>
