<?php include "head.tpl.php"; ?>
<h3><?php _e("Access Control List") ?></h3>
<span class="setting-description"><?php _e("From this page you can share posts in categories, posts with particular tags, single posts, pages or a combination of all with contact: "); echo $contact->display_name ?></span>
<form method="post" action="<?php echo $url.htmlentities('&sub='.$sub) ?>" enctype="application/x-www-form-urlencoded; charset=utf-8">
  <input type="hidden" name="pn_action" value="pn-add-acl" />
  <table border="0" cellpadding="0" cellspacing="0" class="form-table">
	<tr valign="top">
	  <td colspan="3">
		<input type="reset" class="button-secondary" />
		<span class="setting-description"><?php _e("Reset Form") ?></span>
	  </td>
	</tr>
	<tr valign="top">
	  <th scope="row">
		<label for="pn_categories"><?php _e("Choose Categories") ?></label>
	  </th>
	  <td style="white-space: nowrap">
		<select style="height:6.5em;" multiple="multiple" name="pn_categories[]" id="pn_categories">
		  <?php $categories = $db->getCategories(); ?>
		  <?php foreach ($categories as $cat) { ?>
		  <option value="<?php echo $cat->cat_ID ?>"><?php echo $cat->cat_name ?></option>
		  <?php } ?>
		</select>
		<span class="setting-description"><?php _e("Select 0 or more categories to share") ?></span>
	  </td>
	  <td>
		<input type="checkbox" name="pn_cat_inc_public" value="1" <?php if ($fp->cat_inc_public == "yes") { ?>checked="checked"<?php } ?> />
		<span class="setting-description"><?php _e("Check to include public posts in selected categories, if unchecked only private posts are shared") ?></span>
	  </td>
	</tr>
	<tr>
	  <th scope="row">
		<label for="pn_tags"><?php _e("Choose Tags") ?></label>
	  </th>
	  <td style="white-space: nowrap">
		<select style="height:6.5em;" multiple="multiple" name="pn_tags[]" id="pn_tags">
		  <?php $tags = $db->getTags(array('hide_empty' => false)) ?>
		  <?php foreach ($tags as $tag) { ?>
		  <option value="<?php echo $tag->term_id ?>"><?php echo $tag->name ?></option>
		  <?php } ?>
		</select>
		<span class="setting-description"><?php _e("Select 0 or more tags to share") ?></span>
	  </td>
	  <td>
		<input type="checkbox" name="pn_tag_inc_public" value="1" <?php if ($fp->tag_inc_public == "yes") { ?>checked="checked"<?php } ?> />
		<span class="setting-description"><?php _e("Check to include public posts in selected tags, if unchecked only private posts are shared") ?></span>
	  </td>
	</tr>
	<tr>
	  <th scope="row">
		<label for="pn_posts"><?php _e("Choose Posts") ?></label>
	  </th>
	  <td style="white-space: nowrap">
		<select style="height:6.5em;" multiple="multiple" name="pn_posts[]" id="pn_posts">
		  <?php $posts = $db->getPosts('private') ?>
		  <optgroup label="<?php _e('Private Posts') ?>">
		  <?php foreach ($posts as $p) { ?>
		  <option value="<?php echo $p->ID ?>"><?php echo $p->post_title ?></option>
		  <?php } ?>
		  </optgroup>
		  <?php $posts = $db->getPosts('publish') ?>
		  <optgroup label="<?php _e('Public Posts') ?>">
		  <?php foreach ($posts as $p) { ?>
		  <option value="<?php echo $p->ID ?>"><?php echo $p->post_title ?></option>
		  <?php } ?>
		  </optgroup>
		</select>
		<span class="setting-description"><?php _e("Select 0 or more posts to share") ?></span>
	  </td>
	  <td>&nbsp;</td>
	</tr>
	<tr>
	  <th scope="row">
		<label for="pn_pages"><?php _e("Choose Pages") ?></label>
	  </th>
	  <td style="white-space: nowrap">
		<select style="height:6.5em;" multiple="multiple" name="pn_pages[]" id="pn_pages">
		  <?php $pages = $db->getPages('private') ?>
		  <optgroup label="<?php _e('Private Pages') ?>">
			<?php foreach ($pages as $p) { ?>
			<option value="<?php echo $p->ID ?>"><?php echo $p->post_title ?></option>
			<?php } ?>
		  </optgroup>
		  <?php $pages = $db->getPages('publish') ?>
		  <optgroup label="<?php _e('Public Pages') ?>">
			<?php foreach ($pages as $p) { ?>
			<option value="<?php echo $p->ID ?>"><?php echo $p->post_title ?></option>
			<?php } ?>
		  </optgroup>
		</select>
		<span class="setting-description"><?php _e("Select 0 or more pages to share") ?></span>
	  </td>
	  <td>&nbsp;</td>
	</tr>
  </table>
  <p class="submit">
	<input class="button-primary" type="submit" value="<?php _e('Share Selected') ?>"/>
  </p>
</form>
<h3><?php _e("Shared Items") ?></h3>
<form method="post" action="<?php echo $url.htmlentities('&sub='.$sub) ?>" enctype="application/x-www-form-urlencoded; charset=utf-8">
  <input type="hidden" name="pn_action" value="pn-delete-acl" />
  <table class="widefat" border="0" cellpadding="0" cellspacing="0">
	<thead>
	  <tr>
		<th class="check-column" scope="col">
		  <input type="checkbox" />
		</th>
		<th scope="col"><?php _e("Type") ?></th>
		<th scope="col"><?php _e("Name") ?></th>
		<th scope="col"><?php _e("Info") ?></th>
	  </tr>
	</thead>
	<tfoot>
	  <tr>
		<th class="check-column" scope="col">
		  <input type="checkbox" />
		</th>
		<th scope="col"><?php _e("Type") ?></th>
		<th scope="col"><?php _e("Name") ?></th>
		<th scope="col"><?php _e("Info") ?></th>
	  </tr>
	</tfoot>
	<tbody>
	  <?php if ($acls->size() == 0) { ?>
	  <tr>
		<td colspan="4" align="center">
		  <span class="setting-description">
			<?php echo __("Not items are currently shared with: ").$contact->display_name; ?>
		  </span>
		</td>
	  </tr>
	  <?php } else { foreach ($acls as $acl) { ?>
	  <tr>
		<th class="check-column" scope="row">
		  <input type="checkbox" value="<?php echo $acl->id; ?>" name="checked[]" />
		</th>
		<td><strong><?php echo $acl->share_type; ?></strong></td>
		<td><?php echo $acl->display_name; ?></td>
		<td><span class="setting-description">
		   <?php 
			  if ($acl->share_type == 'category') 
				  { 
					  if ($fp->cat_inc_public == 'yes') 
						  { 
							  _e("Sharing Private and Public posts in this category"); 
						  } 
					  else 
						  {
							  _e("Sharing only Private posts in this category"); 
						  } 
				  } 
				  if ($acl->share_type == 'tag') 
					  { 
						  if ($fp->tag_inc_public == 'yes' ) 
							  { 
								  _e( "Sharing Private and Public posts tagged with this tag");
							  } 
						  else 
							  { 
								  _e("Sharing only Private posts with this tag"); 
							  } 
					  } 
    			   if ($acl->share_type == 'post') 
					   {
						   $status = get_post_status($acl->share_id);
						   if ($status == 'private') {
							   _e("Private Post");
						   }
						   else {
							   _e("Public Post");
						   }
					   }
    			   if ($acl->share_type == 'page') 
					   {
						   $status = get_post_status($acl->share_id);
						   if ($status == 'private') {
							   _e("Private Page");
						   }
						   else {
							   _e("Public Page");
						   }
					   }
					?>
			</span>																						
		</td>
	  </tr>
	  <?php } } ?>
	</tbody>
  </table>
  <p class="submit">
	<input class="button-primary" name="pn_delete_acl" type="submit" value="<?php _e('Delete') ?>" />
  </p>  
</form>
<?php include "foot.tpl.php"; ?>
