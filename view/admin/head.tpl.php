<?php 
if (strstr($sub, 'contact') !== FALSE) {
	$title = "&raquo; Contacts";
}
else if ($sub == "certificate") {
	$title = "&raquo; Certificate";
}
?>
<div class="wrap">
  <h2>Private Network <?php echo $title ?></h2>
  <?php if (isset($fp)) { ?>
  <?php if ($fp->hasError('Fatal')) { ?><div class="error"><?php echo nl2br($fp->getError('Fatal')) ?></div>
  <?php } else if (strlen($fp->getMsg()) > 0) { ?>
  <div id="message" class="updated fade" style="background-color: rgb(255, 251, 204);">
	<p><strong><?php echo $fp->getMsg(); ?></strong></p>
  </div><?php } } ?>
  <ul id="pn-submenu">
	<li><a <?php if (empty($sub)) { ?>class="current"<?php } ?> href="<?php echo $url ?>">Private Network</a><span class="pn-sep">|</span></li>
	<li><a <?php if ($sub == 'certificate') { ?>class="current"<?php } ?> href="<?php echo $url.htmlentities('&sub=certificate') ?>"><?php _e('Certificate') ?></a><span class="pn-sep">|</span></li>
	<li>
	  <a <?php if ($sub == 'contacts') { ?>class="current"<?php } ?> href="<?php echo $url.htmlentities('&sub=contacts') ?>"><?php _e('Contacts') ?></a>
	  <?php if (strstr($sub, 'contact_') !== FALSE) { ?><span class="pn-sep">&raquo;</span><?php } ?>
	</li>
	<?php if (strstr($sub, 'contact_') !== FALSE) { ?>
	<li>
	  <a class="current" href="<?php echo $url.htmlentities("&sub=contact_$contact->id") ?>"><?php echo $contact->display_name; ?></a>
	</li>
	<?php } ?>
  </ul>
