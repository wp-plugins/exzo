<?php
/*
Plugin Name: ExZo

Plugin URI: http://blog.vimagic.de/exif-zoom-wordpress-plugin/

Description: Displays Images (JPG), the corresponding Exif (if available) and provides zoom functionality (based on Lightbox).

Version: 0.b6.1

Author: Thomas M. B&ouml;sel
Author URI: http://blog.vimagic.de/

Lisense : GPL(http://www.gnu.org/copyleft/gpl.html)
/*  Copyright 2006  Thomas M. Bosel  (email : tmb@vimagic.de, site : http://blog.vimagic.de)
**
**  This program is free software; you can redistribute it and/or modify
**  it under the terms of the GNU General Public License as published by
**  the Free Software Foundation; either version 2 of the License, or
**  (at your option) any later version.
**
**  This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
**  along with this program; if not, write to the Free Software
**  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class WpExZo {
	////////////////////
	// USER-VARIABLES //
	////////////////////
	var $user_html_title;			// TITLE HTML STRING
	var $user_html_picture;			// CURRENTLY UNUSED
	var $user_html_exif_ON;			// EXIF HTML STRING
	var $user_html_exif_OFF;		// EXIF HTML STRING OFF
	var $user_show_title;			// 0 = OFF, 1 = ON, IF AVAILABLE, 2 = ALWAYS ON
	var $user_show_exif;			// 0 = OFF, 1 = ON, IF AVAILABLE, 2 = ALWAYS ON
	var $user_max_horizontal_pic;	// MAX IMAGE WIDTH
	var $user_max_vertical_pic;		// MAX IMAGE HEIGHT
	var $user_min_table;			// MIN TABLE WIDTH
	var $user_autho_email;			// AUTHOR EMAIL FOR OVERLAY LINKING
	var $user_link_text;			// OVERLAY LINK TEXT
	var $user_link_text_static;		// OVERLAY STATIC TEXT
	var $user_not_available;		// ERROR MSG STRING FOR UNAVAILIBLE EXIF TAGS
	var $user_untitled;				// ERROR MSG STRING FOR EMPTY TITLE
	var $user_preview_filename;		// PREVIEW FILENAME
	var $user_border;				// WIDTH OF BORDER
	var $user_align;				// ALIGN STRING 0=OFF, 1=LEFT, 2=CENTER, 3=RIGHT

	//////////////////
	// VARIABLES 	//
	//////////////////
	var $tableWidth;
	var $title;
	var $EXIF;
	var $imgWidth;
	var $imgHeight;
	var $zoom;
	var $GO;
	var $token;
	var $token_hidden;
	
	//////////////////////////////////////////////////////////////////////////////
	// WpExZo()  PRELIMINARIES (INITIALIZING VARIABLES							//
	//////////////////////////////////////////////////////////////////////////////
	function WpExZo() {
		$this->user_html_title="<table width=\"%tableWidth%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"exif\"><tr valign=\"middle\"><td class=\"header_last\" width=\"50\">Title</td><td class=\"content_bright_last\">&nbsp;&nbsp;&nbsp;</td><td class=\"content_bright_last\" width=\"%tableWidth-50%\">%title%</td></tr></table>";
		$this->user_html_picture="test_picture";
		$this->user_html_exif_ON="<table width=\"%tableWidth%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"exif\">
<tr valign=\"middle\"><td class=\"header\">Cam&Lens</td><td class=\"content_dark\">&nbsp;</td><td class=\"content_dark\">%CAM% & %LENS%</td><td width=\"30\">&nbsp;&nbsp;&nbsp;</td><td class=\"header\">Shutter:</td><td class=\"content_bright\">&nbsp;</td><td class=\"content_bright\">%SHUTTER%</td></tr>
<tr valign=\"middle\"><td class=\"header\">Flash:</td><td class=\"content_dark\">&nbsp;</td><td class=\"content_dark\">%FLASH%</td><td>&nbsp;&nbsp;&nbsp;</td><td class=\"header\">Aperture:</td><td class=\"content_bright\">&nbsp;</td><td class=\"content_bright\">%APERTURE%</td></tr>
<tr valign=\"middle\"><td class=\"header\">Create Date:</td><td class=\"content_bright\">&nbsp;</td><td class=\"content_bright\">%DATETIME%</td><td>&nbsp;&nbsp;&nbsp;</td><td class=\"header\">ISO:</td><td class=\"content_bright\">&nbsp;</td><td class=\"content_bright\">%ISO%</td></tr>
<tr valign=\"middle\"><td class=\"header_last\">Image&nbsp;Number:</td><td class=\"content_dark_last\">&nbsp;</td><td class=\"content_dark_last\">%IMAGENUMBER%</td><td>&nbsp;&nbsp;&nbsp;</td><td class=\"header_last\">Focal&nbsp;Length:</td><td class=\"content_bright_last\">&nbsp;</td><td class=\"content_bright_last\">%FOCAL%</td></tr></table>";
		$this->user_html_exif_OFF="<table width=\"%tableWidth%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"exif\"><tr valign=\"middle\"><td class=\"content_bright_last_center\">no EXIF found</td></tr></table>";
		$this->user_max_horizontal_pic=500;
		$this->user_max_vertical_pic=375;
		$this->user_min_table=400;
		$this->user_show_title=2;
		$this->user_show_exif=1;
		$this->user_author_email='';
		$this->user_link_text='';
		$this->user_link_text_static='picture by';
		$this->user_not_available='n.a.';
		$this->user_untitled='untitle picture';
		$this->user_preview_filename='exzo_dummy.jpg';
		$this->user_border=30;
		$this->user_align=2;
		
		$this->options= array (
			'user_html_title' 		=> 'html',
			'user_html_picture' 	=> 'html',
			'user_html_exif_ON' 	=> 'html',
			'user_html_exif_OFF' 	=> 'html',
			'user_max_horizontal_pic' 	=> 'int',
			'user_max_vertical_pic' 	=> 'int',
			'user_min_table' 		=> 'int',
			'user_show_title' 		=> 'int',
			'user_show_exif' 		=> 'int',
			'user_author_email' 	=> 'string',
			'user_link_text' 		=> 'string',
			'user_link_text_static'	=> 'string',
			'user_not_available'	=> 'string',
			'user_untitled'			=> 'string',
			'user_preview_filename'	=> 'string',
			'user_border' 			=> 'int',
			'user_align' 			=> 'int'
		);
		
		$this->pluginURL = trailingslashit(get_settings('siteurl')) . 'wp-content/plugins/exzo';
		add_filter('the_content', array(&$this, '_filter'), 2);
		add_action('wp_head', array(&$this, '_wpHead'));
		
		$this->title="TITLE";
		$this->GO=0;
		$this->zoom=1;
		$this->token=array('COMPRESSION','PHOTOMETRIC_INTERPRETATION','MODEL','MAKE','ORIENTATION','X_RESOLUTION','Y_RESOLUTION','RESOLUTION_UNIT','SOFTWARE','ARTIST','COPYRIGHT','SHUTTER','FNUMBER','EXPOSURE_PROGRAM','ISO','DATE_TIME_ORIGINAL','DATE_TIME_DIGITIZED','SHUTTER_SPEED_VALUE','APERTURE','EXPOSURE_BIAS_VALUE','MAX_APERTURE_VALUE','METERING_MODE','LIGHT_SOURCE','FLASH','FOCAL','COLOR_SPACE','PIXEL_X_DIMENSION','PIXEL_Y_DIMENSION','SENSING_METHOD','SCENE_TYPE','EXPOSURE_MODE','WHITE_BALANCE','DIGITAL_ZOOM_RATIO','FOCAL_LENGTH_IN_35MM_FILM','SCENE_CAPTURE_TYPE','GAIN_CONTROL','CONTRAST','SATURATION','SHARPNESS','SUBJECT_DISTANCE_RANGE','DATETIME','CAM','LENS','IMAGENUMBER');
		$this->token_hidden=array('Make','Model','ImageWidth','ImageLength','SamplesPerPixel','PhotometricInterpretation','XResolution','YResolution','ResolutionUnit','Compression','PlanarConfiguration','Orientation','ExposureTime','ShutterSpeedValue','FNumber','ApertureValue','ExposureProgram','DateTimeOriginal','DateTimeDigitized','ExposureBiasValue','MaxApertureValue','MeteringMode','LightSource','Fired','Return','Mode','Function','RedEyeMode','FocalLength','SensingMethod','FileSource','SceneType','FocalLengthIn35mmFilm','CustomRendered','ExposureMode','WhiteBalance','SceneCaptureType','GainControl','Contrast','Saturation','Sharpness','SubjectDistanceRange','DigitalZoomRatio','PixelXDimension','PixelYDimension','ColorSpace','ModifyDate','CreatorTool','Rating','CreateDate','MetadataDate','LensInfo','Lens','ImageNumber','format','ColorMode','ICCProfile','History');
	}

	//////////////////////////////////////////////////////////////////////////////
	// _installSettings()  ADDING OPTION VARIABLES TO THE WP DATABASE			//
	//////////////////////////////////////////////////////////////////////////////
	function _installSettings() {
		add_option('exzo_user_html_title', $this->user_html_title,'TITLE HTML STRING','no');
		add_option('exzo_user_html_picture', $this->user_html_picture,'PICTURE HTML STRING','no');
		add_option('exzo_user_html_exif_ON', $this->user_html_exif_ON,'EXIF HTML STRING','no');
		add_option('exzo_user_html_exif_OFF', $this->user_html_exif_OFF,'EXIF HTML STRING','no');
		add_option('exzo_user_max_horizontal_pic', $this->user_max_horizontal_pic,'MAX PIC SIZE (HORI)','no');
		add_option('exzo_user_max_vertical_pic', $this->user_max_vertical_pic,'MAX PIC SIZE (VERT)','no');
		add_option('exzo_user_min_table', $this->user_min_table,'MIN TABLE SIZE (HORI)','no');
		add_option('exzo_user_show_title', $this->user_show_title,'TITLE ON?','no');
		add_option('exzo_user_show_exif', $this->user_show_exif,'EXIF ON?','no');
		add_option('exzo_user_author_email', $this->user_author_email,'AUTHOR EMAIL STRING','no');
		add_option('exzo_user_link_text', $this->user_link_text,'ON PICTURE LINK STRING','no');
		add_option('exzo_user_link_text_static', $this->user_link_text_static,'ON PICTURE STATIC STRING','no');
		add_option('exzo_user_not_available', $this->user_not_available,'STRING FOR N.A. EXIF TAGS','no');
		add_option('exzo_user_untitled', $this->user_untitled,'STRING FOR UNTITLED PICTURES','no');
		add_option('exzo_user_preview_filename', $this->user_preview_filename,'FILENAME FOR PREVIEW PICTURE','no');
		add_option('exzo_user_border', $this->user_border,'BORDER IN PIXEL','no');
		add_option('exzo_user_align', $this->user_align,'ALIGN TABLE','no');
	}

	//////////////////////////////////////////////////////////////////////////////
	// _getSetting()  FETCHING OPTION VARIABLES FROM THE WP DATABASE			//
	//////////////////////////////////////////////////////////////////////////////
	function _getSetting() {
		foreach ($this->options as $option => $type) {
			$this->$option = get_option('exzo_'.$option);
			switch ($type) {
				case 'bool':
				case 'int':
					$this->$option = intval($this->$option);
					break;
				case 'string':
					$value = strval($_POST[$option]);
					break;
				case 'array':
					$this->$option=explode(",",$this->$option); // MAKING ARRAY FROM COMMA SEPARATED STRING
					break;
			}
		}
	}

	//////////////////////////////////////////////////////////////////////////////
	// _getSetting()  UPDATING OPTION VARIABLES IN THE WP DATABASE				//
	//////////////////////////////////////////////////////////////////////////////
	function _updateSettings() {
		foreach ($this->options as $option => $type) {
			if (isset($_POST[$option])) {
				switch ($type) {
					case 'int':
						$value = intval($_POST[$option]);
						break;
					case 'string':
						$value = strval($_POST[$option]);
						break;
					case 'array':
						$value = strtolower(strval($_POST[$option]));
						break;
					case 'bool':
						if(intval($_POST[$option]))	{
							$value = 1;
						}
						else	{
							$value = 0;
						}
						break;
					default:
						$value = stripslashes($_POST[$option]);
				}
				update_option('exzo_'.$option, $value);
			}
			else {
				update_option('exzo_'.$option, $this->$option);
			}
		}
		header('Location: '.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=exzo.php&updated=true');
		die();
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// _wpHead()  ADDING REFERENCE TO CS-SHEET IN HEADER OF HTML				//
	//////////////////////////////////////////////////////////////////////////////
	function _wpHead() {
		echo '<!-- START Wp-ExZo -->';
		echo '<link rel="stylesheet" type="text/css" media="screen" href="'.$this->pluginURL.'/exzo.css" />';
		echo '<script type="text/javascript" src="'.$this->pluginURL.'/zoom/zoom.js"></script>';
		echo '<!-- END Wp-ExZo -->';
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// _fetchExif()  FETCHING EXIF FROM THE IMAGE FILE 							//
	//////////////////////////////////////////////////////////////////////////////
	function _fetchExif($name,$extension,$img_path)	{
		require_once('pel_lw/PelJpeg.php');
		/////////////////////////////////////////////////////////////////
		// DETERMINE, WETHER WE CAN ZOOM OR NOT (THUMBNAIL AVAILABLE?) //
		/////////////////////////////////////////////////////////////////
		if(!file_exists($img_path_smal=str_replace('jpg','thumbnail.jpg',$img_path)))	{
			$this->zoom=0;
			$jpeg = new PelJpeg($img_path);
			$jpeg_noexif = ImageCreateFromJpeg($img_path);
		}
		else	{
			$this->zoom=1;
			$jpeg = new PelJpeg($img_path_smal);
			$jpeg_noexif = ImageCreateFromJpeg($img_path_smal);
		}
		$this->GO=1;
		$exif = $jpeg->getExif();										##################
		if($this->GO && $exif != NULL)	{$tiff = $exif->getTiff();}		### TRYING TO  ###
		else	{$this->GO=0;}											### AVOID ANY  ###
		if($this->GO && $tiff != NULL)	{$ifd0 = $tiff->getIfd();}		### ERROR MSGS ###
		else	{$this->GO=0;}											##################
		if($this->GO && $ifd0 != NULL)	{$exif = $ifd0->getSubIfd(PelIfd::EXIF);}
		else	{$this->GO=0;}			
		
		if($this->GO)	{
			//////////////////////////////////////////////////////
			// XTRACTING RDF DATA WRITTEN BY PS					//
			// THIS IS WHERE WE GET THE ADDITIONAL AUX INFOS	//
			//////////////////////////////////////////////////////
			$adoberdf = "http://ns.adobe.com/xap/1.0/\0";
			$i = 0;
			$rdf = $jpeg->getSection(PelJpegMarker::APP1, $i);
			while ($rdf != null &&
				strncmp($adoberdf, $rdf->getBytes(), strlen($adoberdf)) != 0) {
				$i++;
				$rdf = $jpeg->getSection(PelJpegMarker::APP1, $i);
			}
			if ($rdf != null)	{$rdfxml = substr($rdf->getBytes(), strlen($adoberdf));}

			include_once('arc/arc_rdfxml_parser_source.php');
			$parser=new ARC_rdfxml_parser($parser_args);
			$parser->parse_data($rdfxml);
			$triples=$parser->get_triples($rdfxml);
			if(is_array($triples)){
				for($i=0,$i_max=count($triples);$i<$i_max;$i++){
					$cur_t=$triples[$i];
					$cur_p=$cur_t["p"];
					$cur_p = preg_replace('/.*\/(.*?)/', '$1', $cur_p);
					$cur_o=$cur_t["o"];
					$cur_o_type=$cur_o["type"];
					if($cur_o_type==="uri")			{$foo=$cur_o["uri"];}
					elseif($cur_o_type==="bnode")	{$foo=$cur_o["bnode_id"];}
					elseif($cur_o_type==="literal")	{
						$foo=$cur_o["val"];
						if($dt=$cur_o["dt"])		{/*$foo.=" (dt: ".$dt.")";*/}
						elseif($lang=$cur_o["lang"]){/*$foo.=" (lang: ".$lang.")";*/}
    				}
    				$this->EXIF[$cur_p]=$foo;
  				}
			}
		
			//////////////////////////											###############################################
			// FETCHING EXIF DATA	//											### NOTE: INSTEAD OF READING IT ALL, I ONLY	###
			//////////////////////////											### WANT A DEFINED STATE ON THESE TOKENS 	###
			// IDF0	 //															###	MORE MIGHT BE ADDED IF NEEDED BY USERS	###
			///////////															###############################################
			if($ifd0->getEntry(PelTag::COMPRESSION) != NULL)		$this->EXIF['COMPRESSION'] 		= $ifd0->getEntry(PelTag::COMPRESSION)->getText();
			if($ifd0->getEntry(PelTag::PHOTOMETRIC_INTERPRETATION) != NULL)	$this->EXIF['PHOTOMETRIC_INTERPRETATION'] = $ifd0->getEntry(PelTag::PHOTOMETRIC_INTERPRETATION)->getText();
			if($ifd0->getEntry(PelTag::MODEL) != NULL)				$this->EXIF['MODEL'] 			= $ifd0->getEntry(PelTag::MODEL)->getText();
			if($ifd0->getEntry(PelTag::MAKE) != NULL)				$this->EXIF['MAKE'] 			= $ifd0->getEntry(PelTag::MAKE)->getText();
			if($ifd0->getEntry(PelTag::ORIENTATION) != NULL)		$this->EXIF['ORIENTATION'] 		= $ifd0->getEntry(PelTag::ORIENTATION)->getText();
			if($ifd0->getEntry(PelTag::X_RESOLUTION) != NULL)		$this->EXIF['X_RESOLUTION'] 	= $ifd0->getEntry(PelTag::X_RESOLUTION)->getText();
			if($ifd0->getEntry(PelTag::Y_RESOLUTION) != NULL)		$this->EXIF['Y_RESOLUTION'] 	= $ifd0->getEntry(PelTag::Y_RESOLUTION)->getText();
			if($ifd0->getEntry(PelTag::RESOLUTION_UNIT) != NULL)	$this->EXIF['RESOLUTION_UNIT'] 	= $ifd0->getEntry(PelTag::RESOLUTION_UNIT)->getText();
			if($ifd0->getEntry(PelTag::SOFTWARE) != NULL)			$this->EXIF['SOFTWARE'] 		= $ifd0->getEntry(PelTag::SOFTWARE)->getText();
			if($ifd0->getEntry(PelTag::ARTIST) != NULL)				$this->EXIF['ARTIST'] 			= $ifd0->getEntry(PelTag::ARTIST)->getText();
			if($ifd0->getEntry(PelTag::COPYRIGHT) != NULL)			$this->EXIF['COPYRIGHT'] 		= $ifd0->getEntry(PelTag::COPYRIGHT)->getText();
			///////////
			// EXIF	 //
			///////////
			if($exif->getEntry(PelTag::EXPOSURE_TIME) != NULL)		$this->EXIF['SHUTTER']				= $exif->getEntry(PelTag::EXPOSURE_TIME)->getText();
			if($exif->getEntry(PelTag::FNUMBER) != NULL)			$this->EXIF['FNUMBER']				= $exif->getEntry(PelTag::FNUMBER)->getText();
			if($exif->getEntry(PelTag::EXPOSURE_PROGRAM) != NULL)	$this->EXIF['EXPOSURE_PROGRAM']		= $exif->getEntry(PelTag::EXPOSURE_PROGRAM)->getText();
			if($exif->getEntry(PelTag::ISO_SPEED_RATINGS) != NULL)	$this->EXIF['ISO'] 					= $exif->getEntry(PelTag::ISO_SPEED_RATINGS)->getText();
			if($exif->getEntry(PelTag::DATE_TIME_ORIGINAL) != NULL)	$this->EXIF['DATE_TIME_ORIGINAL']	= $exif->getEntry(PelTag::DATE_TIME_ORIGINAL)->getText();
			if($exif->getEntry(PelTag::DATE_TIME_DIGITIZED) != NULL)$this->EXIF['DATE_TIME_DIGITIZED']	= $exif->getEntry(PelTag::DATE_TIME_DIGITIZED)->getText();
			if($exif->getEntry(PelTag::SHUTTER_SPEED_VALUE) != NULL)$this->EXIF['SHUTTER_SPEED_VALUE']	= $exif->getEntry(PelTag::SHUTTER_SPEED_VALUE)->getText();
			if($exif->getEntry(PelTag::APERTURE_VALUE) != NULL)		$this->EXIF['APERTURE']				= $exif->getEntry(PelTag::APERTURE_VALUE)->getText();
			if($exif->getEntry(PelTag::EXPOSURE_BIAS_VALUE) != NULL)$this->EXIF['EXPOSURE_BIAS_VALUE']	= $exif->getEntry(PelTag::EXPOSURE_BIAS_VALUE)->getText();
			if($exif->getEntry(PelTag::MAX_APERTURE_VALUE) != NULL)	$this->EXIF['MAX_APERTURE_VALUE']	= $exif->getEntry(PelTag::MAX_APERTURE_VALUE)->getText();
			if($exif->getEntry(PelTag::METERING_MODE) != NULL)		$this->EXIF['METERING_MODE']		= $exif->getEntry(PelTag::METERING_MODE)->getText();
			if($exif->getEntry(PelTag::LIGHT_SOURCE) != NULL)		$this->EXIF['LIGHT_SOURCE']			= $exif->getEntry(PelTag::LIGHT_SOURCE)->getText();
			if($exif->getEntry(PelTag::FLASH) != NULL)				$this->EXIF['FLASH'] 				= $exif->getEntry(PelTag::FLASH)->getText();
			if($exif->getEntry(PelTag::FOCAL_LENGTH) != NULL)		$this->EXIF['FOCAL'] 				= $exif->getEntry(PelTag::FOCAL_LENGTH)->getText();
			if($exif->getEntry(PelTag::COLOR_SPACE) != NULL)		$this->EXIF['COLOR_SPACE']			= $exif->getEntry(PelTag::COLOR_SPACE)->getText();
			if($exif->getEntry(PelTag::PIXEL_X_DIMENSION) != NULL)	$this->EXIF['PIXEL_X_DIMENSION']	= $exif->getEntry(PelTag::PIXEL_X_DIMENSION)->getText();
			if($exif->getEntry(PelTag::PIXEL_Y_DIMENSION) != NULL)	$this->EXIF['PIXEL_Y_DIMENSION']	= $exif->getEntry(PelTag::PIXEL_Y_DIMENSION)->getText();
			if($exif->getEntry(PelTag::SENSING_METHOD) != NULL)		$this->EXIF['SENSING_METHOD']		= $exif->getEntry(PelTag::SENSING_METHOD)->getText();
			if($exif->getEntry(PelTag::SCENE_TYPE) != NULL)			$this->EXIF['SCENE_TYPE']			= $exif->getEntry(PelTag::SCENE_TYPE)->getText();
			if($exif->getEntry(PelTag::EXPOSURE_MODE) != NULL)		$this->EXIF['EXPOSURE_MODE']		= $exif->getEntry(PelTag::EXPOSURE_MODE)->getText();
			if($exif->getEntry(PelTag::WHITE_BALANCE) != NULL)		$this->EXIF['WHITE_BALANCE']		= $exif->getEntry(PelTag::WHITE_BALANCE)->getText();
			if($exif->getEntry(PelTag::DIGITAL_ZOOM_RATIO) != NULL)	$this->EXIF['DIGITAL_ZOOM_RATIO']	= $exif->getEntry(PelTag::WHITE_BALANCE)->getText();
			if($exif->getEntry(PelTag::FOCAL_LENGTH_IN_35MM_FILM) != NULL)	$this->EXIF['FOCAL_LENGTH_IN_35MM_FILM']= $exif->getEntry(PelTag::FOCAL_LENGTH_IN_35MM_FILM)->getText();
			if($exif->getEntry(PelTag::SCENE_CAPTURE_TYPE) != NULL)	$this->EXIF['SCENE_CAPTURE_TYPE']	= $exif->getEntry(PelTag::SCENE_CAPTURE_TYPE)->getText();
			if($exif->getEntry(PelTag::GAIN_CONTROL) != NULL)		$this->EXIF['GAIN_CONTROL']			= $exif->getEntry(PelTag::GAIN_CONTROL)->getText();
			if($exif->getEntry(PelTag::CONTRAST) != NULL)			$this->EXIF['CONTRAST']				= $exif->getEntry(PelTag::CONTRAST)->getText();
			if($exif->getEntry(PelTag::SATURATION) != NULL)			$this->EXIF['SATURATION']			= $exif->getEntry(PelTag::SATURATION)->getText();
			if($exif->getEntry(PelTag::SHARPNESS) != NULL)			$this->EXIF['SHARPNESS']			= $exif->getEntry(PelTag::SHARPNESS)->getText();
			if($exif->getEntry(PelTag::SUBJECT_DISTANCE_RANGE) != NULL)		$this->EXIF['SUBJECT_DISTANCE_RANGE']	= $exif->getEntry(PelTag::SUBJECT_DISTANCE_RANGE)->getText();
			if($exif->getEntry(PelTag::SHARPNESS) != NULL)			$this->EXIF['SHARPNESS']			= $exif->getEntry(PelTag::SHARPNESS)->getText();

			//////////////////////////////////////////////
			// 	SOME MODIFICATIONS TO PERSONAL FLAVOR	//
			//  FEEL FREE TO CHANGE OR DELETE EM ;)		//
			//////////////////////////////////////////////
			$this->EXIF['DATETIME']	= preg_replace('#(.*?)-(.*?)-(.*?)T(.*)\+(.*)#','$1.$2.$3&nbsp;$4+$5',$this->EXIF['DateTimeOriginal']);
			$this->EXIF['CAM'] 				= str_replace('NIKON','',$this->EXIF['MODEL']);
			$this->EXIF['SHUTTER']			= str_replace(' sec.','&nbsp;s',$this->EXIF['SHUTTER']);
			$this->EXIF['FOCAL'] 			= str_replace('.0 mm','&nbsp;mm',$this->EXIF['FOCAL']);
			$foo=explode('/',$this->EXIF['X_RESOLUTION']);
			if(is_numeric($foo[1]) && $foo[1]!=0) {$this->EXIF['X_RESOLUTION']=intval($foo[0])/intval($foo[1]);}
			$foo=explode('/',$this->EXIF['Y_RESOLUTION']);
			if(is_numeric($foo[1]) && $foo[1]!=0) {$this->EXIF['Y_RESOLUTION']=intval($foo[0])/intval($foo[1]);}
			$foo=explode('/',$this->EXIF['SHUTTER_SPEED_VALUE']);
			$bar=explode(' ',$foo[1]);
			if(is_numeric($bar[0]) && $bar[0]!=0) {$this->EXIF['SHUTTER_SPEED_VALUE']=intval($foo[0])/intval($bar[0]);}
			array_shift($bar);
			foreach($bar as $ba)	{$this->EXIF['SHUTTER_SPEED_VALUE'].=' '.$ba;}
			if(isset($this->EXIF['Lens']))	{$this->EXIF['LENS'] = $this->EXIF['Lens'];}
			else	{$this->EXIF['LENS'] = 'no lens info';}
			$this->EXIF['IMAGENUMBER'] = $this->EXIF['ImageNumber'];
		}
		$this->imgWidth	= ImageSX($jpeg_noexif);
		$this->imgHeight= ImageSY($jpeg_noexif);
	}
	
	
	//////////////////////////////////////////////////////////////////////////////
	//	exzo()  FILTER FOR DISPLAYING TITLE+PICUTRE+EXIF OF AN IMAGE			//
	//////////////////////////////////////////////////////////////////////////////
	function _exzo($name, $extention, $urll, $title) {
		//////////////////////////////////
		// GETTING SETTINGS & FILEPATHS //
		//////////////////////////////////
		$this->_getSetting();
		if($this->user_preview_filename=='exzo_dummy.jpg' && $title=='exzo_dummy_title')	{
			$img_path=ABSPATH.'wp-content/plugins/exzo/images/exzo_dummy.jpg';
			$img_path_html=$this->pluginURL.'/images/exzo_dummy.jpg';
		}
		else	{
			$file_name 		= stripslashes(trim($name)) . '.' .stripslashes(trim($extention));
			$option 		= stripslashes(trim($option));
			$img_path 		= $this->_getImagePath($file_name,1);
			$img_path_html 	= $this->_getImagePath($file_name,0);
		}
		if($title=='exzo_dummy_title')	{$title='This will be your image title';}

		////////////////////
		// ERROR HANDLING //
		////////////////////
		if(!file_exists($img_path)) 	{
			if($img_path=='')	{return '<p>No Image File Provided</p>';}
			else	{return '<p>Image file not found(ImagePath:'.$img_path.')</p>';}
		}
		if($title != '')	{$this->title=$title;}
		else				{$this->title=$this->user_untitled;}

		//////////////////
		// LETS ROCK!!!	//
		//////////////////
		$this->_fetchExif($name,$extension,$img_path);
		
		///////////////////////////////////////////////////////
		// 	SORTING IMAGE AND TABLE SIZES					 //
		///////////////////////////////////////////////////////		    
		if($this->imgWidth<=$this->imgHeight)	{					//PORTRAIT
			if($this->imgHeight>$this->user_max_vertical_pic)	{	//PIC TOO HIGH
				$this->imgWidth=$this->user_max_vertical_pic*$this->imgWidth/$this->imgHeight;
				$this->imgHeight=$this->user_max_vertical_pic;
			}
		}
		else	{
			if($this->imgWidth>$this->user_max_horizontal_pic)	{	//PIC TOO WIDE
				$this->imgHeight=$this->user_max_horizontal_pic*$this->imgHeight/$this->imgWidth;
				$this->imgWidth=$this->user_max_horizontal_pic;
			}
		}
		if($this->imgWidth+(2*$this->user_border)<$this->user_min_table)	{
			$this->tableWidth=$this->user_min_table;
		}
		else	{
			$this->tableWidth=$this->imgWidth+(2*$this->user_border);
		}
	
		////////////////////////////
		// ASSAMBLING HTML-STRING //
		////////////////////////////
		$print_exif="<!-- BEGIN ExZo v.0b6 -->";
		switch($this->user_align)	{
			case 0:
				break;
			case 1:
				$print_exif="<div align=\"left\">";
				break;
			default:
			case 2:
				$print_exif="<div align=\"center\">";
				break;
			case 3:
				$print_exif="<div align=\"right\">";
				break;
		}
		
		// TITLE - NOW USERDEFINED - WHOOHOO! //
		if($this->user_show_title==2 || ($this->user_show_title==1&&$title!=""))	{$print_exif.=$this->_substitute($this->user_html_title);}
		
		// PICTURE //
		if($this->user_link_text_static=='' && $this->user_link_text=='')	{$spacer=0;}
		else	{$spacer=15;}
		
		
		$print_exif.="<table width=\"".($this->imgWidth+4+(2*$this->user_border))."\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
		$print_exif.="<tr>";
		if($this->zoom)	{	/* SHOWING THUMBNAIL OF REFERENCED IMAGE */
	  	   	$print_exif.="<td><div class=\"iemahge\" style=\"background-image:url(".($img_path_html_smal=str_replace('jpg','thumbnail.jpg',$img_path_html))."); width: ".$this->imgWidth."px; height: ".$this->imgHeight."px\"><div class=\"OpaDiv\">";
 			$print_exif.="<a href=\"$img_path_html\" rel=\"zoom\" title=\"$title\" class=\"imagelinks\"><img src=\"".$this->pluginURL."/zoom/lupe2.png\" alt=\"Click to enlarge picture\" /></a>";
			if($urll!="")	{
				$print_exif.="<a href=\"$urll\" title=\"Read full article\" class=\"imagelinks\"><img src=\"".$this->pluginURL."/images/empty.gif\" height=\"18\" width=\"".($this->imgWidth-18)."\" border=\"0\" alt=\"$title\" /></a><br />";
  			   	$print_exif.="<a href=\"$urll\" title=\"Read full article\" class=\"imagelinks\"><img src=\"".$this->pluginURL."/images/empty.gif\" height=\"".($this->imgHeight-$spacer-18)."\" width=\"".$this->imgWidth."\" border=\"0\" alt=\"".$title."\" /></a><br />";
			}
			else {
				$print_exif.="<a href=\"$img_path_html\" rel=\"zoom\" title=\"$title\" class=\"imagelinks\"><img src=\"".$this->pluginURL."/images/empty.gif\" height=\"18\" width=\"".($this->imgWidth-18)."\" border=\"0\" alt=\"$title\" /></a><br />";
  		   		$print_exif.="<a href=\"$img_path_html\" rel=\"zoom\" title=\"$title\" class=\"imagelinks\"><img src=\"".$this->pluginURL."/images/empty.gif\" height=\"".($this->imgHeight-$spacer-18)."\" width=\"".$this->imgWidth."\" border=\"0\" alt=\"".$title."\" /></a><br />";
			}
 		}
 		else	{		/* SHOWING REFERENCED IMAGE */
	  	   	$print_exif.="<td><div class=\"iemahge\" style=\"background-image:url(".($img_path_html)."); width: ".$this->imgWidth."px; height: ".$this->imgHeight."px\"><div class=\"OpaDiv\">";
			if($urll!="")	{
  			   	$print_exif.="<a href=\"$urll\" title=\"Read full article\" class=\"imagelinks\"><img src=\"".$this->pluginURL."/images/empty.gif\" height=\"".($this->imgHeight-$spacer)."\" width=\"".$this->imgWidth."\" border=\"0\" alt=\"".$title."\" /></a><br />";
			}
			else {
  		   		$print_exif.="<img src=\"".$this->pluginURL."/images/empty.gif\" height=\"".($this->imgHeight-$spacer)."\" width=\"".$this->imgWidth."\" border=\"0\" alt=\"".$title."\" /><br />";
			}
 		}
 		if($spacer!=0)	{
		   	if($this->user_author_email!='')	{
			   	$print_exif.="<font class=\"OpaTextStatic\">".$this->user_link_text_static."&nbsp;</font><a href=\"mailto:".$this->user_author_email."?subject=Comments%20on%20".(get_bloginfo('url').$_SERVER['REQUEST_URI'])."\" class=\"OpaText\">".$this->user_link_text."</a>";
			}
			else	{
				$print_exif.="<font class=\"OpaTextStatic\">".$this->user_link_text_static."&nbsp;".$this->user_link_text."</font>";
			}
			$print_exif.="&nbsp;&nbsp;</div>";
		}
		$print_exif.="</div></td></tr>";
		$print_exif.="</table>";


		// EXIF - NOW USERDEFINED - WHOOHOO! //
		if($this->user_show_exif>0)	{
			if($this->GO && ($this->_printExif('FOCAL') != $this->user_not_available || $this->_printExif('ISO') != $this->user_not_available || $this->_printExif('SHUTTER') != $this->user_not_available || $this->_printExif('APERTURE') != $this->user_not_available))     {$print_exif.=$this->_substitute($this->user_html_exif_ON);}
			elseif($this->user_show_exif>1)	{$print_exif.=$this->_substitute($this->user_html_exif_OFF);}
		}
		if($this->user_align>0)	{$print_exif.="</div>";}
		$print_exif.="<!-- END ExZo v.0b6 -->";

		return $print_exif;
	}
	
	//////////////////////////////////////////////////////////////////////////
	//	_getImagePath()  SELFEXPLANITORY ;)									//
	//////////////////////////////////////////////////////////////////////////
	function _getImagePath($img_file, $global) {
		$img_path = "";
		$img_file_mod = str_replace('thumbnail.','',$img_file);
		$bool=0;
		if($img_file_mod != $img_file)	{$bool=1;}
		global $wpdb, $post;
		$query = "SELECT `guid` FROM `".$wpdb->posts."`";
		$query .= " WHERE `post_status` = 'inherit'";
		$query .= " AND `post_mime_type` = 'image/jpeg'";
		if( $results = $wpdb->get_results($query) ) {
			foreach($results as $r) {
				if( basename(trim($r->guid)) == $img_file_mod ) {
					$siteurl = trailingslashit(get_settings('siteurl'));
					$related_path = str_replace($siteurl, '', $r->guid);
					if($global==1)	{$img_path = ABSPATH . $related_path;}
					else	{$img_path = get_settings('siteurl') .'/'. $related_path;}
					break;
				}
			}
		}
		if($bool)	{$img_path = str_replace('jpg','thumbnail.jpg',$img_path);}
		return $img_path;
	}

	//////////////////////////////////////////////////////////////////////////
	//	_filter()  SELFEXPLANITORY ;)										//
	//////////////////////////////////////////////////////////////////////////
	function _filter($text) {
		$text = str_replace('TMB_PERM', get_permalink(),$text);
		$text = preg_replace('#\[exzo.url="(.*?)".title="(.*?)".*?\](.*?)\.(jpg|jpeg)(.*?)\[/exzo\]#sie', '$this->_exzo(\'$3\', \'$4\', \'$1\',\'$2\')', $text);
		return $text;
	}
	
	//////////////////////////////////////////////////////////////////////////
	//	ADMIN OPTION PANEL													//
	//////////////////////////////////////////////////////////////////////////
	function _options_form() {
		switch($this->user_show_title)	{
			case 0:
				$title_off=' checked="checked"';
				break;
			case 1:
				$title_on=' checked="checked"';
				break;
			default:
			case 2:
				$title_aon=' checked="checked"';
				break;
		}
		switch($this->user_show_exif)	{
			case 0:
				$exif_off=' checked="checked"';
				break;
			case 1:
				$exif_on=' checked="checked"';
				break;
			default:
			case 2:
				$exif_aon=' checked="checked"';
				break;
		}
		switch($this->user_align)	{
			case 0:
				$align_off=' checked="checked"';
				$align_string='[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Alignment:&nbsp;off</font>]';
				break;
			case 1:
				$align_left=' checked="checked"';
				$align_string='[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Alignment:&nbsp;left</font>]';
				break;
			default:
			case 2:
				$align_center=' checked="checked"';
				$align_string='[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Alignment:&nbsp;center</font>]';
				break;
			case 3:
				$align_right=' checked="checked"';
				$align_string='[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Alignment:&nbsp;right</font>]';
				break;
		}
		print('
			<form name="exzo" action="'.get_bloginfo('wpurl').'/wp-admin/options-general.php" method="post">
			<input type="hidden" name="exzo_action" value="exzo__updateSettings" />
			<div class="wrap">
				<h2>'.__('&#187; General ExZo Setup', 'blog.vimagic.de').'</h2>
					<fieldset class="options">
						<table border="0" cellspacing="5" cellpadding="0">
						<tr><td align="right" width="250">
						<strong><label for="user_max_horizontal_pic">'.__('(max)&nbsp;Thumbnail&nbsp;width&nbsp;(panoramic):', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td align="left">
						<input type="text" name="user_max_horizontal_pic" size="4" value="'.$this->user_max_horizontal_pic.'" />px
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Maximal horizontal image size (which is my thumbnail width for panoramic pictures)</font>]</td></tr>
						</td></tr>
						<tr><td align="right">
						<strong><label for="user_max_vertical_pic">'.__('(max) Thumbnail height (portrait):', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td align="left">
						<input type="text" name="user_max_vertical_pic" size="4" value="'.$this->user_max_vertical_pic.'" />px
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Maximal vertical image size (which is my thumbnail heigth for portrait pictures)</font>]</td></tr>
						</td></tr>
						<tr><td align="right">
						<strong><label for="user_min_table">'.__('(min) Table width:', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td align="left">
						<input type="text" name="user_min_table" size="4" value="'.$this->user_min_table.'" />px
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Minimal table width for the Title and Exif templates (when defined using the <code>%TableWidth%</code> token).  This asserts that we don\'t get tables that are too narrow to hold the wanted Exif infos (which would possibly break them).</font>]</td></tr>
						</td></tr>
						<tr><td align="right">
						<strong><label for="user_border">'.__('Image Border:', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td align="left">
						<input type="text" name="user_border" size="4" value="'.$this->user_border.'" />px
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">To calculate to acutal <code>%TableWidth%</code> we need to know the image border as defined in the <code>exzo.css</code> (<code>.iemahge</code> class)</font>]</td></tr>
						</td></tr>
						<tr><td align="right" rowspan="4" valign="middle"><strong>
						<label for="user_align">'.__('Align:', 'blog.vimaigc.de').'</label></strong></td><td>&nbsp;</td><td align="left">
						<input type="radio" name="user_align" value="1" '.$align_left.'/>&nbsp;left
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Adding <code>&lt;div align="left"&gt;...&lt;/div&gt;</code> around Title, Image and Exif containers.</font>]</td></tr>
						<tr><td>&nbsp;</td><td align="left" width="80">
						<input type="radio" name="user_align" value="2" '.$align_center.'/>&nbsp;center
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Adding <code>&lt;div align="center"&gt;...&lt;/div&gt;</code> around Title, Image and Exif containers.</font>]</td></tr>
						<tr><td>&nbsp;</td><td align="left">
						<input type="radio" name="user_align" value="3" '.$align_right.'/>&nbsp;right
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Adding <code>&lt;div align="right"&gt;...&lt;/div&gt;</code> around Title, Image and Exif containers.</font>]</td></tr>
						<tr><td>&nbsp;</td><td align="left" valign="top">
						<input type="radio" name="user_align" value="0" '.$align_off.'/>&nbsp;off
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">No additional alignemnt used.  Note: It is not advised to use alignment options spanning from Title to Exif container within the templates, unless you are sure that both are simultaneously on or off (in which case they\'d be useless though).</font>]</td></tr>
						</table>
						<table border="0" cellspacing="5" cellpadding="0">
						<tr><td align="right" valign="top" width="250">
						<strong><label for="user_author_email">'.__('Overlay E-Mail:', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td>
						<input type="text" name="user_author_email" size="25" value="'.$this->user_author_email.'" />
						</td><td>&nbsp;</td><td rowspan="3">
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">If provided, these informations will be used to display a semi-transparent overlayed text at the bottom of each picture.  The structure is <code>Overlay Static Text Overlay Linked Text</code> where the later will be linked via <code>mailto:</code> to the <code>Overlay E-Mail</code> (no link if empty).  Leaving both the <code>Overlay Static Text</code> and <code>Overlay Linked Text</code> empty will deactivate the overlay.</font>]</td></tr>
						</td></tr>
						<tr><td align="right" valign="top">
						<strong><label for="user_link_text_static">'.__('Overlay&nbsp;Static&nbsp;Text:', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td>
						<input type="text" name="user_link_text_static" size="25" value="'.$this->user_link_text_static.'" />
						</td></tr>
						<tr><td align="right" valign="top">
						<strong><label for="user_link_text">'.__('Overlay Linked Text:', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td>
						<input type="text" name="user_link_text" size="25" value="'.$this->user_link_text.'" />
						</td></tr>
						</table>
						<p class="submit"><input type="submit" name="submit_buttom" value="'.__('Update ExZo Settings', 'blog.vimaigc.de').'" /></p>
					</fieldset>
				</div>');
		print('
			<div class="wrap">
				<h2>'.__('&#187; Title Template', 'blog.vimagic.de').'</h2>
					<fieldset class="options">
						<table border="0" cellspacing="5" cellpadding="0">
						<tr><td align="right" rowspan="3" valign="middle"><strong>
						<label for="user_show_title">'.__('Title:', 'blog.vimaigc.de').'</label></strong></td><td>&nbsp;</td><td align="left">
						<input type="radio" name="user_show_title" value="0" '.$exif_off.'/> off
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Disables the Title container.</font>]</td></tr>
						<tr><td>&nbsp;</td><td align="left">
						<input type="radio" name="user_show_title" value="1" '.$exif_on.'/> on if available
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">On iff the title is available.</font>]</td></tr>
						<tr><td>&nbsp;</td><td align="left">
						<input type="radio" name="user_show_title" value="2" '.$title_aon.'/> always on
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">self explanitory :)</font>]</td></tr>
						<tr><td align="right" valign="top"><strong>
						<label for="user_untitled">'.__('Untitled Picture:', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td>
						<input type="text" name="user_untitled" size="25" value="'.$this->user_untitled.'" />
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Used for untitled pictures - that is, if the picture was called with <code>title=""</code>.  This string may be empty.</font>]
						</td></tr>
						<tr><td align="right" valign="top"><strong>
						<label for="user_html_title">'.__('Title HTML template:', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td colspan="3">
						<textarea name="user_html_title" cols="100" rows="5">'.htmlentities($this->user_html_title).'</textarea>
						</td></tr>
						</table>
						<p class="submit"><input type="submit" name="submit_buttom" value="'.__('Update ExZo Settings', 'blog.vimaigc.de').'" /></p>
					</fieldset>
			</div>');

		print('
			<div class="wrap">
				<h2>'.__('&#187; Exif Template', 'blog.vimagic.de').'</h2>
					<fieldset class="options">
						<table border="0" cellspacing="5" cellpadding="0">
						<tr><td align="right" rowspan="3" valign="middle"><strong>
						<label for="user_show_exif">'.__('Exif:', 'blog.vimaigc.de').'</label></strong></td><td>&nbsp;</td><td align="left">
						<input type="radio" name="user_show_exif" value="0" '.$exif_off.'/> off
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Disables the Exif container.</font>]</td></tr>
						<tr><td>&nbsp;</td><td align="left">
						<input type="radio" name="user_show_exif" value="1" '.$exif_on.'/> on if available
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">On iff at least one of &quot;the core values&quot; (aperture, shutter, ISO and focal length) is not empty.</font>]</td></tr>
						<tr><td>&nbsp;</td><td align="left">
						<input type="radio" name="user_show_exif" value="2" '.$exif_aon.'/> always on
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">self explanitory :)</font>]</td></tr>
						<tr><td align="right" valign="top"><strong>
						<label for="user_not_available">'.__('Single Exif n.a.:', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td>
						<input type="text" name="user_not_available" size="25" value="'.$this->user_not_available.'" />
						</td><td>&nbsp;</td><td>
						[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">This string will be substituted for empty Exif fields.  This string may be empty.</font>]
						</td></tr>
						<tr><td align="right" valign="top"><strong>
						<label for="user_html_exif_ON">'.__('Exif HTML template:', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td colspan="3">
						<textarea name="user_html_exif_ON" cols="100" rows="16">'.htmlentities($this->user_html_exif_ON).'</textarea>
						</td></tr>
						<tr><td align="right" valign="top"><strong>
						<label for="user_html_exif_OFF">'.__('Exif HTML template:<br />(No Exif data found)', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td colspan="3">
						<textarea name="user_html_exif_OFF" cols="100" rows="5">'.htmlentities($this->user_html_exif_OFF).'</textarea>
						</td></tr>
						</table>
						<p class="submit"><input type="submit" name="submit_buttom" value="'.__('Update ExZo Settings', 'blog.vimaigc.de').'" /></p>
					</fieldset>
				</div>
			');
		$preview = explode('.j', $this->user_preview_filename);
		
		print('
			<div class="wrap">
				<h2>'.__('&#187; Preview', 'blog.vimagic.de').'</h2>
				<fieldset class="options">
					<table border="0" cellspacing="5" cellpadding="0">
						<tr><td align="right" valign="top">
						<strong><label for="user_preview_filename">'.__('Filename:', 'blog.vimaigc.de').'</label></strong>
						</td><td>&nbsp;</td><td align="left">
						<input type="text" name="user_preview_filename" size="25" value="'.$this->user_preview_filename.'" />&nbsp;&nbsp;&nbsp;[<font style="color:#bbb;font-weight:bold;font-size:0.75em;">Filename including extension for the preview image.  Token values in the overview below are gathered from this image as well.</font>]
						</td></tr>
						<tr><td align="right" valign="top">
						<strong><label for="user_preview_filename">'.__('Preview:', 'blog.vimaigc.de').'</label></strong><br />
						'.$align_string.'
						</td><td>&nbsp;</td><td align="left">
						'.$this->_exzo($preview[0], 'j'.$preview[1], '','exzo_dummy_title').'
						</td></tr>						
					</table>
					<p class="submit"><input type="submit" name="submit_buttom" value="'.__('Update ExZo Settings', 'blog.vimaigc.de').'" /></p></form>
				</fieldset>
			</div>
			');

		print('
			<div class="wrap">
				<h2>'.__('&#187; Usable Tokens', 'blog.vimagic.de').'</h2>');
		print('<div align="center"><table border="0" cellpadding="3" cellspacing="5">');
		print('<tr><td colspan="3" style="border: 1px #000 dotted;"><strong>%title%</strong> - will be substituted for the tile of the image</td></tr>');
		print('<tr><td colspan="3" style="border: 1px #000 dotted;"><strong>%tableWidth%</strong> - width of the table for title/exif-table container (function of image size, border and minimal table size)</td></tr>');
		print('<tr><td colspan="3" style="border: 1px #000 dotted;"><strong>%tableWidth+/-n%</strong> - basic algebraic manipulation of the table width (adding OR substraction n pix from the table width)</td></tr>');
		print('<tr><td colspan="3" style="border: 1px #000 dotted;"><strong>%PERMALINK%</strong> - will get substituted by the permalink of the post/page of the picture</td></tr>');
		print('<tr><td valign="top"><table border="0" cellpadding="2" cellspacing="0" style="border-bottom: 1px #000 dotted;"><tr><td align="right" style="background:#FD7F7F;border-left: 1px #000 dotted;border-right: 1px #000 dotted;border-top: 1px #000 dotted;"><strong>Exif token</strong></td><td style="background:#FD7F7F;border-top: 1px #000 dotted;">&nbsp;</td><td align="left" style="background:#FD7F7F;border-left: 1px #000 dotted;border-right: 1px #000 dotted;border-top: 1px #000 dotted;">value for the preview image</td></tr>');
		sort($this->token);
		$oddoreven=0;
		foreach($this->token as $token)	{
			if($oddoreven==0)	{
				$oddoreven=1;
				print('<tr"><td align="right" style="background:#BFDEFF;border: 1px #000 dotted;"><strong>%'.$token.'%</strong></td><td style="background:#BFDEFF;border-top: 1px #000 dotted;border-bottom: 1px #000 dotted;">&nbsp;&nbsp;-&nbsp;&nbsp;</td><td align="left" style="background:#BFDEFF;border: 1px #000 dotted;">'.$this->_printExif($token).'</td></tr>');
			}
			else	{
				$oddoreven=0;
				print('<tr"><td align="right" style="background:#D9ECFF;border-left: 1px #000 dotted;border-right: 1px #000 dotted;"><strong>%'.$token.'%</strong></td><td style="background:#D9ECFF;">&nbsp;&nbsp;-&nbsp;&nbsp;</td><td align="left" style="background:#D9ECFF;border-right: 1px #000 dotted;border-left: 1px #000 dotted;">'.$this->_printExif($token).'</td></tr>');
			}
		}
		print('</table>');
		print('</td><td>&nbsp;&nbsp;</td><td valign="top">');
		print('<table border="0" cellpadding="2" cellspacing="0" style="border-bottom: 1px #000 dotted;"><tr><td align="right" style="background:#FD7F7F;border-left: 1px #000 dotted;border-right: 1px #000 dotted;border-top: 1px #000 dotted;"><strong>PS token</strong></td><td style="background:#FD7F7F;border-top: 1px #000 dotted;">&nbsp;</td><td align="left" style="background:#FD7F7F;border-left: 1px #000 dotted;border-right: 1px #000 dotted;border-top: 1px #000 dotted;">value for the preview image</td></tr>');
		sort($this->token_hidden);
		$oddoreven=0;
		foreach($this->token_hidden as $token)	{
			if($oddoreven==0)	{
				$oddoreven=1;
				print('<tr"><td align="right" style="background:#FEF0DC;border: 1px #000 dotted;"><strong>%'.$token.'%</strong></td><td style="background:#FEF0DC;border-top: 1px #000 dotted;border-bottom: 1px #000 dotted;">&nbsp;&nbsp;-&nbsp;&nbsp;</td><td align="left" style="background:#FEF0DC;border: 1px #000 dotted;">'.$this->_printExif($token).'</td></tr>');
			}
			else	{
				$oddoreven=0;
				print('<tr"><td align="right" style="background:#FFE4BF;border-left: 1px #000 dotted;border-right: 1px #000 dotted;"><strong>%'.$token.'%</strong></td><td style="background:#FFE4BF;">&nbsp;&nbsp;-&nbsp;&nbsp;</td><td align="left" style="background:#FFE4BF;border-right: 1px #000 dotted;border-left: 1px #000 dotted;">'.$this->_printExif($token).'</td></tr>');
			}
		}
		print('</table></td></tr></table></div>');				
		print('</div>');
	}

	//////////////////////////////////////////////////////////////////////////////
	// _substitute()  FILTER TOKENS AND SUBSITUTE FOR CORRESPONDING EXIF		//
	//////////////////////////////////////////////////////////////////////////////
	function _substitute($foo)	{		### MAINLY TOKEN HANDLING ###
		$foo = preg_replace('/%tableWidth([+,-]?)(\d*?)%/e', "\$this->_calc('$this->tableWidth','\\1','\\2')", $foo);
		$foo = preg_replace('/%title%/', $this->title, $foo);
		$foo = preg_replace('/%PERMALINK%/', get_permalink(), $foo);
		$foo = preg_replace('/%(.*?)%/e', "\$this->_printExif('\\1')", $foo);
		return $foo;
	}

	//////////////////////////////////////////////////////////////////////////////
	// _printExif()  RETURN EXIF OR DEFINED ERROR MSG							//
	//////////////////////////////////////////////////////////////////////////////
	function _printExif($a)	{
		if(isset($this->EXIF[$a]) && $this->EXIF[$a]!='')	{return $this->EXIF[$a];}
		else	{return $this->user_not_available;}
	}

	//////////////////////////////////////////////////////////////////////////////
	// _calc()  DOING SOME BASIC ALGEBRAIC CALCULATIONS							//
	//////////////////////////////////////////////////////////////////////////////
	function _calc($a,$b,$c)	{
		if($b == "+")		{return intval($a)+intval($c);}
		elseif($b == "-")	{return intval($a)-intval($c);}
		elseif($b == "")	{return intval($a);}
		else	{return -1;}
	}

}
$WpExZo = new WpExZo();

///////////////////////////////////////////////
// ADMIN PANEL FUNCTIONS				 	 //
///////////////////////////////////////////////
function exzo_options_form() {
	global $WpExZo;
	$WpExZo->_options_form();
}

function exzo_options() {						##############################
	if (function_exists('add_options_page')) {	### INSTALLING ADMIN PANEL ###
		add_options_page(						##############################
			__('ExZo Options', 'blog.vimagic.de'),
			__('ExZo', 'blog.vimagic.de'),
			10,
			basename(__FILE__),
			'exzo_options_form'
		);
	}
}

function exzo_header() {						###############################
	global $WpExZo;								### NEEDED FOR CORRECT		###
	$WpExZo->_wpHead();							### PREVIEW IN ADMIN PANEL	###
}												###############################

///////////////////////////////////////////////
// ADDING WORDPRESS ACTIONS				 	 //
///////////////////////////////////////////////
add_action('admin_menu', 'exzo_options');
add_action('admin_head', 'exzo_header');

//////////////////////////////////////////////////////
// SOME FUNCTIONS FOR THE CORRECT EVENT HANDLING	//
//////////////////////////////////////////////////////
if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
		$bInstall=0;
		foreach ($WpExZo->options as $option => $type) {
			if(get_option('exzo_'.$option) == '' )	{
				$bInstall=1;
			}
		}
		if($bInstall)	{$WpExZo->_installSettings();}
}
else	{$WpExZo->_getSetting();}

if (!empty($_POST['exzo_action'])) {
	switch($_POST['exzo_action']) {
		case 'exzo__updateSettings': 
			$WpExZo->_updateSettings();
			break;
	}
}

?>
