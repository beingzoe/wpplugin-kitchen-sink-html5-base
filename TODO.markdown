# TODO for Kitchen Sink HTML5 Base #

A finite list of known projects/tasks intended for the next release.
Other items, typically less formulated, can be found on the Roadmap in the
KST wiki at github: https://github.com/beingzoe/wpplugin-kitchen-sink-html5-base/wiki

If you are going to specifically tackle anything on this list, just
fork the project, send me a message through github letting me know,
and send me a pull request when you are done.

## High priority (v.0.1)
-------------------------------------------------------

### Misc

* create new send mail / Contact class

* Theme support
  * Need to output a clean "directory" list of all developers
  * Only add links to support if the 'help' appliance is loaded

* Make first dev starter theme
  * Finish html5 semantic stuff
    * comments (last big one) etc...
  * check style on all pages (author and so on)
  * post footer margin/float problems ie6-7
    * line-height for tags and categories and such
  * make excerpt, more links cooler

* ON INSTALL/ACTIVATE actions and UNINSTALL/DEACTIVATE
  * Make sure we load first (everytime a KST plugin activates)
  * Have generic hook callback to migrate and database stuff that might change version to version
  * ? what else?
  * For sites being redone from XHTML v0.2 I need a script to migrate the options and custom_fields to new metabox style

* update README.md and README.txt proper


## Lower priority 0.1.1 > 0.2
-------------------------------------------------------

* cycle and scrollable both have so much bullshit still

* See the todo list on the phpdocs for a bunch of other stuff some of which
  could be handled later as they are plugin related. But definitely any
  core/base bullshit.

* Asides class (make awesome, unobtrusive, and truly useful)

* DONE: get rid of stupid image sprite buttons
  * Many client designs use image buttons. Need easy ready-to-go option
