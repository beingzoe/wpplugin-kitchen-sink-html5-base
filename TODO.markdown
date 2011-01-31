# TODO for Kitchen Sink HTML5 Base #

A finite list of known projects/tasks intended for the next release.
Other items, typically less formulated, can be found on the Roadmap in the
KST wiki at github: https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base/wiki

If you are going to specifically tackle anything on this list, just
fork the project, send me a message through github letting me know,
and send me a pull request when you are done.


## Misc ##

* SEO needs updated for all the new stuff and put into bundled_appliances array and presets
  * analytics should be inserted unobtrusivley and not be in templates at all

* create new send mail class

* Theme support
  * dashboard (how to deal with the multiple developers issue?)
  * Only add links to support if the 'help' appliance is loaded
  * page listing all developers

* get rid of stupid image sprite buttons (leave in sample code?)
  * NavPost
  * NavPosts
  * Search Form
  * anywhere else?

* Make first dev starter theme
  * Go through TwentyTen and make it FINAL for 0.1 stable-ish release tag
  * Finish html5 semantic stuff
    * comments (last big one) etc...
  * attachment page and lightobxing seems funked up
  * check style on all pages (author and so on)
  * post footer margin/float problems ie6-7
    * line-height for tags and categories and such
  * make excerpt, more links cooler
  * microformats in footer need a permanent solution


* ON INSTALL/ACTIVATE actions and UNINSTALL/DEACTIVATE
  * Make sure we load first (everytime a KST plugin activates)
  * Have generic hook callback to migrate and database stuff that might change version to version
  * ? what else?
  * For sites being redone from XHTML v0.2 I need a script to migrate the options and custom_fields to new metabox style

* update README.md and README.txt proper

* add boilerplate .htaccess to package somehow (documentation?)

* MetaBoxes should be created with arrays like optionsgroups (with the option to use a template)

* cycle and scrollable both have so much bullshit still

* See the todo list on the phpdocs for a bunch of other stuff some of which
  could be handled later as they are plugin related. But definitely any
  core/base bullshit.

* JIT message
  * flies all the way across the screen in ie7
  * jit_message needs to be fixed so it isn't relying on the drop shadow existing and can handle more generic presentation with better defaults
    * template?

* Asides class (make awesome, unobtrusive, and truly useful)

* Additional Image Sizes plugin redone and bundled version as appliance
  * Or possibly just redo then submodule/subtree it so it always exists but can be maintained separately
* Sociable plugin submoduled/subtreed so that it always exists but is maintained separately?

* mp3player could have more customizability
  * Find safe HTML5 solution for this and video

* create default page layout options (see those other themes with 12 layout templates to choose from or some shit
* cool handy multipurpose shortcodes
  * special formatted boxes
    * error
    * note
* cool forms? js/image replacement?

* Regarding options - review add_settings_field and add_settings_section
  which allow adding options and even whole sections of options to an
  existing page (not a whole new menu - just the options to be added)


## Help File(s) System ##

Need a simple but robust way for each kitchen and appliance to extend the bundled
help. In the original KST XHTML base theme I had a ridiculously clumsy way of
handling this that was not maintainable (and somewhat embarassing in retrospect)
but worked fine since at that time each custom client theme could not be
automatically updated anyway.

Final Plan:
/my_kitchen/help/...

Directory structure ideally would match the intended pattern of the output
but doesn't matter.

Create an array of your help files:

$array = array(
            array (
                'title' => 'Intro to WP',
                'page' => 'Using WordPress',
                'section' => 'Post thumbnails',
                'path' => '/relative/or/not/is/the/question'
                ),
            array (
                'title' => 'Intro to WP',
                'page' => 'Using WordPress',
                'section' => 'Post thumbnails',
                'path' => '/relative/or/not/is/the/question'
                ),
);

Standard pages
    Theme help
    Theme options
    Features
    Using WordPress
    Blog Posts
    Site Pages (cms)
    Media
    Plugins
    Settings
    Developer notes


is_admin() && just viewing any page but a help page
    parse and merge the arrays
        just return all the pages and make it unique
            create the menus (add_submenu_page)

is_admin() && about to view a particular page)
    parse and merge the arrays
        just the elements that have the appropriate $page value
            make a new array with this and sort alphabetically?
                loop
                    create toc
                loop
                    include templates



plugins/
  kitchen-sink-html5-base/
    docs/
      wordpress/
        intro_to_wordpress.html
      theme/
        customizing_icons.html
      [etc.]
  kst-extra-bonus-appliances/
    docs/
      tetris_plugin.html
      [etc.]
themes/
  custom-theme/
    docs/
      plugins/
        using_paypal_because_you_are_dumb.html
      theme_notes/
        style_guide.html

kst/init.php
if (is_admin()) {
  $core->registerHelpFile('wordpress/intro_to_wordpress.html', array('title' => 'Intro to WP', 'section' => 'wordpress'));
  $core->addOptionPage // etc.
}
  [etc.]







