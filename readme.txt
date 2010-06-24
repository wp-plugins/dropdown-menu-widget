=== Shailan Dropdown Menu Widget ===
Contributors: mattsay
Donate link: http://shailan.com/donate  
Tags: css, dropdown, menu, widget, pages, categories, multi
Requires at least: 2.8  
Tested up to: 2.9.2 
Stable tag: 1.5.1

This widget adds a beatiful vertical/horizontal CSS only dropdown menu of pages OR categories of your blog.

== Description ==

Dropdown Menu widget adds a beautiful, CSS only dropdown menu, listing pages OR categories of your blog. It allows you to chose vertical or horizontal. It supports multiple instances. You can select a theme for your widget from the Dropdown Menu Settings page. If you don't like ready-made templates you can create your own theme for the menus using CSS. Hope you like it. Want your own dropdown theme? [Request theme](http://shailan.com/contact)

== Installation ==

1. Upload the plugin to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Appearance -> Widgets to add this widget to one of your sidebars
1. You can also use `<?php shailan_dropdown_menu(); ?>` in your template to display the menu.
1. Don't forget to change menu settings from Settings -> Dropdown Menu panel.

== Frequently Asked Questions ==

= I added this widget to my sidebar but it looks all weird! =

This widget is intented for *wide header widget areas*, not regular sidebars. You can add a sidebar to your theme or you can get a *all-widget* theme from  [shailan.com](http://shailan.com). 

= Can i create my own theme? =

Since this plugin works on CSS, if you are capable of writing CSS, you can customize the theme as you like it.

= I don't know CSS, how can i customize it? = 

Plugin comes with various themes already installed. If you want something different, then you can [request a new theme](http://shailan.com/contact). 

= I found a bug! Where do i submit it? =

You can submit errors and bugs using the [online form](http://shailan.com/contact) on my site.

== Screenshots ==

1. A preview of the widget in action
1. Shiny Black menu theme
1. WP-Default menu theme
1. Dropdown menu settings page
1. New exclude pages metabox

== Changelog ==

= 1.5.1 = 
* Removed custom walker support for now.
* Removed blue tabs theme since it was using custom walkers.
* Fixed all the styles to work with new css classes. 
* Updated Shiny Black theme to work with wide links.
* Complete support for custom css insert.

= 1.5 = 
* Fixed issues with wordpress 3.0.
* Renamed plugin to dropdown menu.
* Removed inline style option.
* Removed unnecessary screenshots.
* Added support for wordpress 3.0 navigation menus.
* Removed exclude pages plugin.

= 1.4.1 = 
* A minor fix for `Parse error: parse error, expecting `T_FUNCTION' in C:\wamp\www\wordpress\wp-content\plugins\dropdown-menu-widget\shailan-multi-dropdown.php on line 194` error. 

= 1.4.0 = 
* Added option for multiline links. If checked a link with more than one word will be wrapped.
* Another fix for IE. Hopefully last.

= 1.3.9 = 
* Fixed errors for IE jquery support. 
* Added belorussian translation provided by [Marcis G.](http://pc.de)
* Added lang folder for translations.

= 1.3.8 =
* Added option for displaying title attributes. You may now turn title display on from the Settings page.

= 1.3.7 =
* Removed unnecessary extrude line from Dropdown Multi widget. 

= 1.3.6 = 
* Fixed "Dropdown Multi" widget error with categories and links.

= 1.3.5 = 
* Added "Dropdown Multi" widget that allows you to inlude pages, categories and links in one menu.

= 1.3.4 = 
* Fixed dropdown errors for IE7. Report any bugs with a screenshot please. Thanks.

= 1.3.3 =
* Fixed function name collisions with "Exclude Pages" plugin. The plugin is fully functional now.

= 1.3.2 =
* Bundled with "Exclude Pages" plugin by [Simon Wheatley](http://simonwheatley.co.uk/wordpress/). You can now easily exclude pages from the navigation. Just uncheck the the "Include page in menus" checkbox on the page edit screen. See [screenshots](http://wordpress.org/extend/plugins/dropdown-menu-widget/screenshots/) for more information.

= 1.3.1 = 
* Added "Blue gradient" theme.

= 1.3.0 = 
* Fixed "Home" link bug for the template tag. Thanks to Jeff.

= 1.2.7 =  
* Added "include homepage link" for both pages and categories now. You can enable/disable this link from the widget options easily.

= 1.2.6 = 
* Fixed a minor bug.

= 1.2.5 = 
* Added translation support. 
* Added pot file for translators.

= 1.2.4 =
* Fixed category walker for the advanced styling.

= 1.2.3 =
* Added Aqua theme.
* Added <span> elements in the menu so more advanced styling can be made.
* Added alignment option. Now you can align your menu wherever you wanted!
* Added Shiny Black theme.

= 1.2.0 =
* Removed title attributes for the categories dropdown menu items.

= 1.1.0 =
* Added custom walker class to disable title attributes on menu items.
* Renamed class and style files.
* Fixed default theme.

= 1.0.0 =
* Added vertical dropdown menu functionality.
* Fixed widget code.
* Changed dropdown widget classname to : shailan-dropdown-menu
* Changed wrapper div classname to : shailan-dropdown-wrapper
* Moved li item paddings to anchor elements.

= 0.4.3 =
* New grayscale theme.
* Template tag `shailan_dropdown_menu()` is available now. See usage for more info.
* Template tag options added.

= 0.4.2 = 
* Fixed XHTML error on link tags.
* Fixed Inline Style error on categories dropdown menu.
* Removed unnecessary files.

= 0.4.1 =
* Fixed XHTML issues.
* Added WP Default theme.
* Made some minor fixes to widget options form.

= 0.3 =
* Fixed problems about styling. Now you can change dropdown menu style from the options page.

= 0.2 = 
* First public release.
* Added login and register button options.

== TODO == 

* Add option for custom menus.
* Add some more themes.. [Request a theme](http://shailan.com/contact)