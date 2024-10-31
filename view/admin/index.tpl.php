<?php include "head.tpl.php"; ?>
<h3 class="title">Private Network</h3>
<p><strong>Private Network</strong> <?php _e('is a WordPress plug-in that allows Administrators at different WordPress installations, for example www.alice.com and www.bob.com, to share their posts within categories, posts with selected tags or single posts and pages. Within categories and tags is possible to share only private or include public posts.') ?>.</p>
<ul>
  <li><a href="#how-does-it-work"><?php _e('How does it work') ?></a></li>
  <li><a href="#customize"><?php _e('Customize') ?></a></li>
  <li><a href="#requirements"><?php _e('Requirements') ?></a></li>
  <li><a href="#disclaimer"><?php _e('Disclaimer') ?></a></li>
  <li><?php _e('For more info please visit:') ?> <a href="http://www.andreabelvedere.com/private-network">http://www.andreabelvedere.com/private-network</a></li>
</ul>
<p><strong><a name="how-does-it-work"><?php _e('How does it work') ?></a>:</strong></p>
<p><?php _e('Say that Alice, the Administrator of www.alice.com wants to share her private posts with her best friend Bob that is the Administrator of www.bob.com;') ?><br />
  <?php _e('this are the steps that both Alice and Bob would have to follow:') ?></p>
<ul>
  <li>&mdash;&gt; <?php _e('Create a') ?> <a href="<?php echo $url.htmlentities('&sub=certificate') ?>">Certificate</a>.</li>
  <li>&mdash;&gt; <?php _e('Visit the') ?> <a href="<?php echo $url.htmlentities('&sub=contacts') ?>"><?php _e('Contacts') ?></a> <?php _e('section.') ?></li>
  <li>&mdash;&gt; <?php _e('Alice will have to enter Bob URL (www.bob.com) and Bob user-name (this is by default “admin”) in the provided text fields.') ?></li>
  <li>&mdash;&gt; <?php _e('Bob contact will appear in Alice&#8217;s contacts list with status') ?> <code>“<?php _e('Awaiting Confirmation') ?>”</code></li>
  <li>&mdash;&gt; <?php _e('Next time Bob logs-in to his site&#8217;s (www.bob.com) admin section and checks his contact list will notice Alice request with status') ?> “<code><?php _e('Confirm Contact') ?></code>”</li>
  <li>&mdash;&gt; <?php _e('Bob approves Alice request by pressing the') ?> <code>“<?php _e('Confirm Contact') ?>”</code> <?php _e('button and then enables her to view his private posts by setting the status to') ?> <code>“<?php _e('Enabled') ?>”</code></li>
  <li>&mdash;&gt; <?php _e('Alice checks her contacts again and sees that Bob has confirmed her as a contact. She copies and paste the tag in her contact list associated with “Bob” into a new post (preferably a private post), saves the post and she can now view the items that Bob has decided to share with Alice.') ?></li>
</ul>
<p><strong><a name="customize"><?php _e('Customize') ?></a></strong></p>
<p>
  <?php _e("To change the look of the retrieved posts customize the following css file: ") ?><code><?php echo "plugins/private-network/css/pn-style.css"; ?></code>
</p>
<p><strong><a name="requirements"><?php _e('Requirements') ?></a></strong></p>
<ul>
  <li><?php _e('PHP 5 with OpenSSL support') ?></li>
</ul>
<p><strong><a name="disclaimer"><?php _e('Disclaimer') ?></a></strong></p>
<p><i><?php _e('Private Network plug-in is provided to you without any warranties, representations or gurantees of any kind, 
	INCLUDING, WITHOUT LIMITATION THE WARRANTY OF MERCHANTABILITY AND WARRANTY OF FITNESS FOR A PARTICULAR PURPOSE.') ?>
</i></p>
<p><i><?php _e('BY USING THE PLUG-IN YOU EXPRESSLY ASSUME ALL RISK OF LOSS ASSOCIATED WITH ANY DATA LOSS OR ANY DAMAGE ALLEDGED TO HAVE 
				BEEN CAUSED BY THE PLUG-IN.') ?></i></p>
<?php include "foot.tpl.php"; ?>
