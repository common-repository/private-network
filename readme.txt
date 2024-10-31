=== Private Network ===
Contributors: AndreaBelvedere
Donate link: http://www.andreabelvedere.com/
Tags: post, page, private, network, private network
Requires at least: 2.5.0
Tested up to: 3.0.4
Stable tag: 1.3

Allows Administrators at different WP installations, for example alice.com and bob.com, to share remotely their private and public posts and pages.

== Description ==

Private Network allows Administrators at different WordPress installations, for example www.alice.com and www.bob.com, to share their posts within categories, posts with selected tags or single posts and pages. Within categories and tags is possible to share only private or include public posts.

= How does it work: =
Lets say that Alice, the Administrator of www.alice.com wants to share her private posts with her best friend Bob, the Administrator of www.bob.com;
this are the steps that both Alice and Bob would have to follow:

* Install Private Network plug-in and create the Certificate as per Installation instructions.
* Go to the admin section of Private Network and click “Contacts”
* Alice will have to enter Bob URL (www.bob.com) and Bob user-name (this is by default “admin”) in the provided text fields.
* Bob contact will appear in Alice's contacts list with status “Awaiting Confirmation”
* Next time Bob logs-in to his site's (www.bob.com) admin section and checks his contact list will notice Alice request with status “Confirm Contact”
* Bob approves Alice request by pressing the “Confirm Contact” button and then enables her to view his private posts setting the status to “Enabled”
* Alice checks her contacts again and sees that Bob has confirmed her as a contact. She copies and paste the tag in her contact list belonging to contact “Bob” into a new post (preferably a private post), saves the post and she can now view the items that Bob has decided to share with Alice.

= Requires =

* PHP 5 and OpenSSL support

= Features =

* Version 1.3 - Again no new feature, bug fixes only, magic methods __get and __post and __isset were declared protected rather then public.
* Version 1.2 - No features enhancement in this release, bug fixes only (i.e. better class autoloading)
* Version 1.1 - Share entire categories, posts with specific tags, single posts or pages.
* Version 1.1 - Possible to share both private and public items
* Version 1.1 - Minor bug fixes
* Version 1.0 - Receive last 10 private posts from added contact
* Version 1.0 - Create/Delete X.509 Certificate, add,remove,enable or disable contacts.

= Bug Fixes =

Non yet, but please report any on http://www.andreabelvedere.com/private-network

== Installation ==

1. Upload `private-network` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create your certificate from `Settings`=> `Private Network` => `Certificate`
4. Add contacts from `Settings` => `Private Network` => `Contacts`
5. For each contact edit the Access Control List

== Frequently Asked Questions ==

Not yet, please ask them here http://www.andreabelvedere.com/private-network

== Screenshots ==

1. Certificate summary view
2. Contacts page view
3. Contact Access Control List

== Notes ==

* Version 1.0 and Version 1.1 are compatible, however in 1.1 Administrators also need to edit the Access Control List (ACL) by adding items that they want to share, if the ACL is empty for a particular contact, that contact will not be able to retrieve any items from your blog.
* Change this css file plugins/private-network/css/pn-style.css to customize the look of the retrieved posts
