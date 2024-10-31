<?php include "head.tpl.php"; ?>
<h3>Add Contact</h3>
<form method="post" action="<?php echo $url.htmlentities('&sub='.$sub) ?>" enctype="application/x-www-form-urlencoded; charset=utf-8">
  <input type="hidden" name="pn_action" value="add_contact" />
  <table border="0" cellpadding="0" cellspacing="0" class="form-table">
	<tr class="form-field form-required">
	  <th scope="row"><label for="contact_url"><?php _e('Contact URL (required)') ?></label></th>
	  <td>
		http://<input id="contact_url" class="regular-text code" type="text" name="contact_url" value="<?php echo $fp->contact_url ?>" />
		<span class="setting-description"><?php _e('URL of WordPress to connect to.') ?></span>
		<?php if ($fp->hasError('contact_url')) { ?><br /><span class="error"><?php echo $fp->getError('contact_url') ?></span><?php } ?>
	  </td>
	</tr>
	<tr class="form-field">
	  <th scope="row"><label for="contact_name"><?php _e('Contact Username') ?></label></th>
	  <td><input id="contact_name" class="regular-text code" type="text" name="contact_name" value="<?php echo $fp->contact_name ?>" />
		<span class="setting-description"><?php _e('If left blank, username "admin" is assumed.') ?></span>
		<?php if ($fp->hasError('contact_name')) { ?><br /><span class="error"><?php echo $fp->getError('contact_name') ?></span><?php } ?>
	  </td>
	</tr>
  </table>
  <p class="submit"><input class="button-primary" type="submit" value="<?php _e('Add') ?>" /></p>
</form>
<?php /* Show contacts */ ?>
<h3>Contacts</h3>
<form method="post" action="<?php echo $url.htmlentities('&sub='.$sub) ?>" enctype="application/x-www-form-urlencoded; charset=utf-8">
  <table class="widefat" border="0" cellpadding="0" cellspacing="0">
	<thead>
	  <tr>
		<th class="check-column" scope="col">
		  <input type="checkbox" />
		</th>
		<th scope="col"><?php _e('Contact') ?></th>
		<th scope="col">URL</th>
		<th scope="col"><?php _e('Email') ?></th>
		<th scope="col"><?php _e('IP') ?></th>
		<th scope="col"><?php _e('Status') ?></th>
		<th scope="col"><?php _e('Tag') ?></th>
	  </tr>
	</thead>
	<tfoot>
	  <tr>
		<th class="check-column" scope="col">
		  <input type="checkbox" />
		</th>
		<th scope="col"><?php _e('Contact') ?></th>
		<th scope="col">URL</th>
		<th scope="col"><?php _e('Email') ?></th>
		<th scope="col"><?php _e('IP') ?></th>
		<th scope="col"><?php _e('Status') ?></th>
		<th scope="col"><?php _e('Tag') ?></th>
	  </tr>
	</tfoot>
	<tbody>
	  <?php foreach ($contacts as $contact) { ?>
	  <tr <?php if ($contact->actionRequired()) { ?>class="unverified"<?php } else { ?>class="verified"<?php } ?>>
		<th class="check-column" scope="row">
		  <input type="checkbox" value="<?php echo $contact->id ?>" name="checked[]" />
		</th>
		<td>
		  <strong><a href="<?php echo $url.htmlentities("&sub=contact_$contact->id") ?>"><?php echo $contact->display_name ?></a></strong>
		  <div class="row-actions">
			<span class="edit">
			  <a title="Edit Access Control List" href="<?php echo $url.htmlentities("&sub=contact_$contact->id") ?>">Edit ACL</a>
			</span>
		  </div>
		</td>
		<td><?php echo $contact->url ?></td>
		<td><?php echo $contact->email ?></td>
		<td><?php echo $contact->ipVerifyStatus() ?></td>
		<td>
		  <?php if ($contact->status != 1) { ?>
		  <input class="button-secondary" type="submit" name="contact_action_<?php echo $contact->id ?>" value="<?php echo $contact->getStatus() ?>" />
		  <?php } else { echo $contact->getStatus(); } ?>
		</td>
		<td><?php if ($contact->identity_key) { ?>[pn-<?php echo $contact->identity_key ?>]<?php } ?></td>
	  </tr>
	  <?php } ?>
	</tbody>
  </table>
  <p class="submit">
	<input class="button-primary" name="delete_contact" type="submit" value="<?php _e('Delete') ?>" />
	<span class="setting-description"><?php _e('There is no undo.') ?></span>
  </p>  
</form>
<?php include "foot.tpl.php"; ?>
