The Short
ExZo is a plugin that provides a filter for displays jpg-pictures, some (or all) of their Exif tags and bundles a zoom functionality (if a larger version of the picture is available). User definable templates and token access to any Exif tag and a life preview of each in the admin panel are core features.
 » For more discussion on the why? read the FAQ below.

Usage
Using this simple token in your post: [exzo url=”" title=”"]image.jpg[/exzo] while running the plug-in will provide this output

You have some options while using this filter. If you supply an url, the image will be linked to that url (all except the magnifying glass at the upper left, which will always enlarge the picture if possible). If no url is supplied (i.e. url=""), the whole image will be linked to the larger image (if available). If there’s no thumbnail file the zoom function will be disabled (you’ll see no loupe) and the whole picture is either linked to the url (if supplied) or static with no interaction.

Requirements / Setup
You will need PHP5 and Wordpress2.1 or higher to use this plugin. Otherwise there is nothing more to set up, just download, extract into /wordpress/wp-content/plugins and activate. Check the admin panel "Options->ExZo" for further options (for example your email if you like).
Note: the pictures should have been uploaded with the internal WordPress uploader - at least an entry in the wp_posts database should exist (needed to find the image file using only the filename).

Templates
As of v0.b6 you can write your own template for the Title and the Exif section. For example the template for my title looks like this:
<table width="%tableWidth%" class="exif">
  <tr valign="middle">
    <td class="header_last" width="50">Title</td>
    <td class="content_bright_last">&nbsp;&nbsp;&nbsp;</td>
    <td class="content_bright_last" width="%tableWidth-50%">%title%</td>
  </tr>
</table>
Notice the token words %tableWidth% and %title%. See the next section for a small token overview.

Usable Tokens
The following alphabetical list shows all tokens provided so far (and their values for the picture above). The left side are your standard Exif information, while the right hand side are grabbed from the PS XML inside the Exif aux field. These might vary if you are not using PS and are hence provided with limited support.

%title% - will be substituted for the tile of the image
%tableWidth% - width of the table for title/exif-table container (function of image size, border and minimal table size)
%tableWidth+/-n% - basic algebraic manipulation of the table width (adding OR substraction n pix from the table width)
%PERMALINK% - will get substituted by the permalink of the post/page of the picture

License
This WordPress plug is released under the GPL and is provided with absolutely no warranty (as if?). For support leave a comment and we’ll see what the community has to say.


Download [v0.b6.1]
ExZo.zip [210.719 byte]	   	
Complete with all needed resources including:
- the Lightbox mod by Jan Van Boghout
- a lightweight PEL distribution
- the appmosphere RDF/XML-parser
- a sample picture (including thumbnail)

FAQ
Q: Modifying inline-uploading.php?
A: Modifying the inline-uploading.php is obsolete as of wordpress v2.1. Click here for a short tutorial on what to do now.

Q: It doesn’t work: Parse error*: parse error, unexpected ')', expecting '(' in *[...]/wordpress/wp-content/plugins/exzo/exzo.php*
A: This is most likely due to the fact that your web server is not running on PHP 5, which we need for PEL.

Q: Why another Exif/Picture/Zoom Plugin?
A: Basically because there is no other plug-in which is able to provide access to all Exif tags (f.e. the image number provided by Nikons D200). If you ask me, most existing plug ins only provide a rudimentary implementation. Apart from that, none of them includes an elegant lightbox-style way of zooming pictures. All in all that was reason enough to write ExZo.

To-Dos / Wishlist
With the new version of ExZo we are back to beta level. So expect bugs and (please) report bugs. This site is of course driven by the latest version so things seem stable over here. But with Exif only being a “recommended standard” people could do anything with the tags. The list of tokens can be extended at any time to your wishes. Just drop a note in the comments as to which Exif tag you are missing.
   » customisable templates
   » admin panel
   » providing easy access to other Exif tags
   » alignment user definable
   » pretty up the admin panel
   » code cleaning

Version History
13.feb.2007	v0.b6.1	» new option: customisable alignment [Off | Left | Center | Right]
» pepped up the admin panel
» optimised code
12.feb.2007	v0.b6	Major (complete) rewrite
» Admin panel
» User definable templates
» Token based access to Exifs
» "Live preview" in admin panel
» new option: max thumbnail width
» new option: max thumbnail height
» new option: min table width
» new option: Title [Off / On / Force On]
» new option: Exif [Off / On / Force On]
» new option: overlay strings [for linked & static text]
» new option: error strings [no title / empty exif tag]
02.feb.2007	v0.5.4	» Bugfix [Corrected Transitional XHTML Code]
26.jan.2007	v0.5.3	» WordPress 2.1 Compatibility Update [adapted to new table wp_post table]
21.jan.2007	v0.5.2	» Bugfix [bug in _getImagePath squashed]
15.jan.2007	v0.5.1	» Bugfix [incorrect camera maker readout]
14.jan.2007	v0.5	» got rid of Perl <sniff> by using the PEL-PHP-Library
08.jan.2007	v0.4	» first public release