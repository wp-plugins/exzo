=== Exif & Zoom ===
Contributors: tmb
Tags: exif, zoom, formating, lightbox, images, picture, photo, foto,
Requires at least: 2.1
Tested up to: 2.6.2
Stable tag: 0.7.1

Displays Images (JPG), the corresponding Exif (if available) and provides zoom functionality (based on Lightbox).

== Description ==

ExZo is a plugin that displays jpgs, some (or all) of their Exif tags and bundles a zoom functionality. User definable templates and token access to any Exif tag and a life preview of each in the admin panel are core features.

**New:** as of version 0.b6.4 you can use ExZo to display Exif only!

**New:** as of version 0.b7 ExZo handles WordPress generated thumbs.

== Installation ==

You will need **PHP5** and **Wordpress2.1** or higher to use this plugin. Otherwise there is nothing more to set up, just <a href="http://downloads.wordpress.org/plugin/exzo.zip">download</a>, extract into `/wordpress/wp-content/plugins` and activate. Check the admin panel "Options->ExZo" for further options (for example your email if you like).

**Note:** the pictures should have been uploaded with the internal WordPress uploader - at least an entry in the `wp_posts` database should exist (needed to find the image file using only the filename).


== Frequently Asked Questions ==

= It doesn't work: Parse error*: parse error, unexpected ')', expecting '(' in *[...]/wordpress/wp-content/plugins/exzo/exzo.php* =
This is most likely due to the fact that your web server is not running on PHP 5, which we need for PEL.

= Why another Exif/Picture/Zoom Plugin? =
Basically because there is no other plug-in which is able to provide access to all Exif tags (f.e. the image number provided by Nikons D200). If you ask me, most existing plug ins only provide a rudimentary implementation. Apart from that, none of them includes an elegant lightbox-style way of zooming pictures. All in all that was reason enough to write ExZo.

== Screenshots ==
1. ExZo in action.
2. Fully configurable in the admin options panel.

== Usage ==

Using this simple token in your post: `[exzo url="" title="title"]image.jpg[/exzo]` while running the plug-in will provide the standard output (title, image and exif). You have some options while using this filter. If you supply an url, the image will be linked to that url (all except the magnifying glass at the upper left, which will always enlarge the picture if possible). If no url is supplied (i.e. `url=""`), the whole image will be linked to the larger image (if available). If there's no thumbnail file the zoom function will be disabled (you'll see no loupe) and the whole picture is either linked to the url (if supplied) or static with no interaction.


As of version v0.b6.5 it's possible to use the token `[exif img="image.jpg]` to display the Exif only.

== Templates ==
As of v0.b6 you can write your own template for the Title and the Exif section. For example the template for my title looks like this:
`<table width="%tableWidth%" class="exif">
  <tr valign="middle">
    <td class="header_last" width="50">Title</td>
    <td class="content_bright_last">&nbsp;&nbsp;&nbsp;</td>
    <td class="content_bright_last" width="%tableWidth-50%">%title%</td>
  </tr>
</table>`
Notice the token words **`%tableWidth%`** and **`%title%`**. See the next section for a small token overview.


== Usable Tokens ==

There's quite a few tokens that are theoretically possible.  Some are dependent on the values set in the admin panel, others are basic EXIF and yet others are EXIF written by software like Photoshop.

= admin panel tokens = 
* **%title%** - will be substituted for the tile of the image
* **%tableWidth%** - width of the table for title/exif-table container (function of image size, border and minimal table size)
* **%tableWidth+/-n%** - basic algebraic manipulation of the table width (adding OR substraction n pix from the table width)
* **%PERMALINK%** - will get substituted by the permalink of the post/page of the picture

= standard EXIF =
the following tokens are standard EXIF tags. Most are self explanatory - I've still added the value of each tag from my sample picture. 

* **%APERTURE%** - f/6.3
* **%ARTIST%** - Thomas M. B&ouml;sel
* **%CAM%** - D200
* **%COLOR_SPACE%** - Uncalibrated
* **%COMPRESSION%** - Uncompressed
* **%CONTRAST%** - Normal
* **%COPYRIGHT%** - Visual Magic (Photographer)
* **%DATETIME%** - 2007.01.01 16:26:50.57+01:00
* **%DATE_TIME_DIGITIZED%** - n.a.
* **%DATE_TIME_ORIGINAL%** - n.a.
* **%DIGITAL_ZOOM_RATIO%** - Auto white balance
* **%EXPOSURE_BIAS_VALUE%** - 0.0
* **%EXPOSURE_MODE%** - Auto exposure
* **%EXPOSURE_PROGRAM%** - Normal program
* **%FLASH%** - Flash did not fire.
* **%FNUMBER%** - f/6.3
* **%FOCAL%** - 26 mm
* **%FOCAL_LENGTH_IN_35MM_FILM%** - 39
* **%GAIN_CONTROL%** - Normal
* **%IMAGENUMBER%** - 7505
* **%ISO%** - 100
* **%LENS%** - 17.0-55.0 mm f/2.8
* **%LIGHT_SOURCE%** - Unknown
* **%MAKE%** - NIKON CORPORATION
* **%MAX_APERTURE_VALUE%** - 30/10
* **%METERING_MODE%** - Pattern
* **%MODEL%** - NIKON D200
* **%ORIENTATION%** - top - left
* **%PHOTOMETRIC_INTERPRETATION%** - RGB
* **%PIXEL_X_DIMENSION%** - 500
* **%PIXEL_Y_DIMENSION%** - 281
* **%RESOLUTION_UNIT%** - Inch
* **%SATURATION%** - High saturation
* **%SCENE_CAPTURE_TYPE%** - Standard
* **%SCENE_TYPE%** - Directly photographed
* **%SENSING_METHOD%** - One-chip color area sensor
* **%SHARPNESS%** - Hard
* **%SHUTTER%** - 1/160 s
* **%SHUTTER_SPEED_VALUE%** - 7.321928 sec. (APEX: 12)
* **%SOFTWARE%** - Adobe Photoshop CS2 Macintosh
* **%SUBJECT_DISTANCE_RANGE%** - Unknown
* **%WHITE_BALANCE%** - Auto white balance
* **%X_RESOLUTION%** - 300
* **%Y_RESOLUTION%** - 300


As this is but a small subset of all possible tokens, see webpage for a complete list.

= PS CS2 EXIF =
Here are some EXIF grabbed from the PS XML inside the Exif aux field. These might vary if you are not using PS and are hence provided with limited support.

* **%ApertureValue%** - 5310704/1000000
* **%ColorMode%** - 3
* **%ColorSpace%** - -1
* **%Compression%** - 1
* **%Contrast%** - 0
* **%CreateDate%** - 2007-01-03T15:49:05+01:00
* **%CreatorTool%** - Adobe Photoshop CS2 Macintosh
* **%CustomRendered%** - 0
* **%DateTimeDigitized%** - 2007-01-01T16:26:50.57+01:00
* **%DateTimeOriginal%** - 2007-01-01T16:26:50.57+01:00
* **%DigitalZoomRatio%** - 1/1
* **%ExposureBiasValue%** - 0/6
* **%ExposureMode%** - 0
* **%ExposureProgram%** - 2
* **%ExposureTime%** - 1/160
* **%FNumber%** - 63/10
* **%FileSource%** - 3
* **%Fired%** - False
* **%FocalLength%** - 260/10
* **%FocalLengthIn35mmFilm%** - 39
* **%Function%** - False
* **%GainControl%** - 0
* **%History%** - n.a.
* **%ICCProfile%** - Adobe RGB (1998)
* **%ImageLength%** - 1925
* **%ImageNumber%** - 7505
* **%ImageWidth%** - 3423
* **%Lens%** - 17.0-55.0 mm f/2.8
* **%LensInfo%** - 170/10 550/10 28/10 28/10
* **%LightSource%** - 0
* **%Make%** - NIKON CORPORATION
* **%MaxApertureValue%** - 30/10
* **%MetadataDate%** - 2007-01-03T15:49:05+01:00
* **%MeteringMode%** - 5
* **%Mode%** - 0
* **%Model%** - NIKON D200
* **%ModifyDate%** - 2007-01-03T15:49:05+01:00
* **%Orientation%** - 1
* **%PhotometricInterpretation%** - 2
* **%PixelXDimension%** - 500
* **%PixelYDimension%** - 281
* **%PlanarConfiguration%** - 1
* **%Rating%** - 1
* **%RedEyeMode%** - False
* **%ResolutionUnit%** - 2
* **%Return%** - 0
* **%SamplesPerPixel%** - 3
* **%Saturation%** - 2
* **%SceneCaptureType%** - 0
* **%SceneType%** - 1
* **%SensingMethod%** - 2
* **%Sharpness%** - 2
* **%ShutterSpeedValue%** - 7321928/1000000
* **%SubjectDistanceRange%** - 0
* **%WhiteBalance%** - 0
* **%XResolution%** - 3000000/10000
* **%YResolution%** - 3000000/10000
* **%format%** - image/jpeg


== License ==
This WordPress plug is released under the <a href="http://www.gnu.org/licenses/gpl.html">GPL</a> and is provided with absolutely no warranty (as if?). For support leave a comment and we'll see what the community has to say.


== Version History ==

* **15.oct.2008 - v0.b7.1** - new feature: thumbnail handling optimized
* **01.oct.2008 - v0.b7** - new option: thumbnail choice
* **06.jun.2008 - v0.b6.7** - new feature: compatible to most gallery plugins
* **05.jun.2008 - v0.b6.6** - bugfix: Date/Time issue solved
* **03.jun.2008 - v0.b6.5** - new feature: all PEL tokens now available

	> - bugfix: iPhone support
	> - bugfix: Date/Time issue addressed

* **02.jun.2008 - v0.b6.4** - new feature: show Exif only
* **06.mar.2008 - v0.b6.3** - new option: customisable thumbnail extension
* **13.feb.2007 - v0.b6.1** - new option: customisable alignment [Off | Left | Center | Right]

	> - pepped up the admin panel
	> - optimised code

* **12.feb.2007 - v0.b6** - Major (complete) rewrite

	> - Admin panel
	> - User definable templates
	> - Token based access to Exifs
	> - "Live preview" in admin panel
	> - new option: max thumbnail width
	> - new option: max thumbnail height
	> - new option: min table width
	> - new option: Title [Off / On / Force On]
	> - new option: Exif [Off / On / Force On]
	> - new option: overlay strings [for linked & static text]
	> - new option: error strings [no title / empty exif tag]

* **02.feb.2007 - v0.5.4** - Bugfix [Corrected Transitional XHTML Code]
* **26.jan.2007 - v0.5.3** - WordPress 2.1 Compatibility Update [adapted to new table wp_post table]
* **21.jan.2007 - v0.5.2** - Bugfix [bug in _getImagePath squashed]
* **15.jan.2007 - v0.5.1** - Bugfix [incorrect camera maker readout]
* **14.jan.2007 - v0.5** - got rid of Perl <sniff> by using the PEL-PHP-Library
* **08.jan.2007 - v0.4** - first public release
