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


## Help File(s) System ##

Need a simple but robust way for each kitchen and appliance to extend the bundled
help. In the original KST XHTML base theme I had a ridiculously clumsy way of
handling this that was not maintainable (and somewhat embarassing in retrospect)
but worked fine since at that time each custom client theme could not be
automatically updated anyway.

Ideally the documentation for each kitchen/appliance would be created and stored
in the files where that help is relevant (i.e. Help for a plugin appliance would
be in the main file of that appliance) and then we somehow aggregate all the
individual disparate help content (on ACTIVATION? each time the appropriate HELP
file is VIEWED in the admin?).

The help would be added to some predefined structure:

page
    section
        ...that help content...

It would also add a TOC entry at the same time based on the section name.
For now I think the Page>Section with no nested sections will work fine.
Ideally though we would be able to also nest sections for more robust help files.

So from the admin menu you would see a top level section under 'Theme Options':

[Theme Help]
WordPress
Theme Notes
Features
Plugins
Dev Notes

Actual predefined pages TBD

So the question is what mechanism to create/store/load/output the disparate help
content? In my fanciful world we would do something like...

On activation?:
$my_kitchen->addHelp($page, $section, $content_title, $content_entry );

That method would then store that information somewhere (new table?) and would
never be asked for again unless being explicitly viewed and minimally to create the menus.

or?
$args = array(
                array('
                    'page' => 'known_help_page',
                    'section' => 'existing_section',
                    'entry_title' => 'TwitterBar',
                    'entry_content' => 'path or content'
                ')
            );

            $my_kitchen->help->add($args);
            $my_kitchen->help->remove($entry_title);
addEntry($page, $section, $entry_title, $entry_content);


Ideas?




