<?php
/*
	Copyright (c) 2005-2016, Guru Sistemas and/or Gustavo Adolfo Arcila Trujillo
	All rights reserved.
	www.gurusistemas.com
	
	phpMyDataGrid Professional IS NOT FREE, may not be re-sold or redistributed as a single library.
	
	If you want to use phpMyDataGrid Professional on any of your projects, you Must purchase a license.
	
	You can buy the full source code or encoded version at http://www.gurusistemas.com/
	also can try the donationware version, which can be downloaded from http://www.gurusistemas.com/
	
	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS  "AS IS"  AND ANY EXPRESS  OR  IMPLIED WARRANTIES, INCLUDING, 
	BUT NOT LIMITED TO,  THE IMPLIED WARRANTIES  OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT
	SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,  INDIRECT,  INCIDENTAL, SPECIAL, EXEMPLARY,  OR CONSEQUENTIAL 
	DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF  USE, DATA, OR PROFITS;  OR BUSINESS 
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
	OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. 
	
	For more info, samples, tips, screenshots, help, contact, support, please visit phpMyDataGrid site  
	http://www.gurusistemas.com/
*/

	class menuright {
		var $MM_DivNames  = array();        /*Name for DIVS to create*/
		var $DIVdata      = array();     	/*Items in each menu*/
		var $actualItem   = 0;        		/*Actual number of item, autoincremented when you do call addmenu function*/
		var $fontStyle    = "font-family:Verdana,Arial;font-size:10px;color:#000;";
		var $sl 		  = "";
		var $br 		  = "";
		var $dgGridID     = "";
		
		function fontstyle($style){ $this->fontStyle = $style;}
		function borderwidth($border){ $this->borderwidth = $border; }
		
		function addmenu($MM_DivName, $width=100, $height=16, $separator=1, $bordersize=1, $useimages=0, $borderColor="#c0c0c0", $color="#ffffff", $onmouseovercolor="#EFEFEF", $style="", $background="#F1F1F1"){
			$this->MM_DivNames[$MM_DivName]["MM_DivName"] = $MM_DivName;
			$this->MM_DivNames[$MM_DivName]["width"] = $width;
			$this->MM_DivNames[$MM_DivName]["separator"] = $separator;
			$this->MM_DivNames[$MM_DivName]["useimages"] = $useimages;
			$this->MM_DivNames[$MM_DivName]["bordercolor"] = $borderColor;
			$this->MM_DivNames[$MM_DivName]["backgcolor"] = $color;
			$this->MM_DivNames[$MM_DivName]["mouseovercolor"] = $onmouseovercolor;
			$this->MM_DivNames[$MM_DivName]["style"] = $style;
			$this->MM_DivNames[$MM_DivName]["height"] = $height;
			$this->MM_DivNames[$MM_DivName]["bgimage"] = $background;
			$this->MM_DivNames[$MM_DivName]["bordersize"] = $bordersize;
		}
		
		function additem($MM_DivName, $displaytext, $link="", $image=""){
			$this->DIVdata[$this->actualItem]["MM_DivName"] = $MM_DivName;
			$this->DIVdata[$this->actualItem]["displaytext"] = $displaytext;
			$this->DIVdata[$this->actualItem]["link"] = $link;
			$this->DIVdata[$this->actualItem]["image"] = $image;
			$this->actualItem++;
		}
		
		function addSeparator($MM_DivName, $sepHeight=1, $sepColor=NULL){/*
		  $MM_DivName (string)  = is the div identification where you want to put the separator;
		  $sepHeight (int)   = the height (in pixies) for the separator, the default is 1px;
		  $sepColor (string) = hexadecimal color (with the # simbol) for the separator, when = NULL the border color of the menu will be assumed */
			$this->DIVdata[$this->actualItem]["MM_DivName"]  = $MM_DivName;
			$this->DIVdata[$this->actualItem]["displaytext"] = "SEPARATOR";
			$this->DIVdata[$this->actualItem]["link"] 		 = "SEPARATOR";
			$this->DIVdata[$this->actualItem]["image"] 		 = "SEPARATOR";
			if( $sepColor === NULL )
				$this->DIVdata[$this->actualItem]['style'] = array($sepHeight,"return $"."color;");
			else
				$this->DIVdata[$this->actualItem]['style'] = array($sepHeight,"return '".$sepColor."';");
			$this->actualItem++;
		}
		
		function creascript(){ 
			$strOutput = "<script type='text/javascript' languaje='javascript' src='js/mmscripts.js'></script>{$this->br}"; 
			if (isset($this->retcode)) return $strOutput; else echo $strOutput;
		}
	
		function onleftclick($div,$MM_parameters=""){ return " onclick=\"return MM_mostrar('$div',event,'$MM_parameters');\"";}
		
		function onclick($div,$MM_parameters=""){ return " oncontextmenu=\"return MM_mostrar('$div',event,'$MM_parameters');\"";}

		function creadivs($withform, $method='POST'){
			$retStr = "";
			foreach ($this->MM_DivNames as $nombre) {
				$stylo= (!empty($nombre["style"]))?$nombre["style"]:'border-style:solid; border-width:'.$nombre["bordersize"].'px; background-color:'.$nombre["backgcolor"].';';
				$width= (!empty($nombre["width"]))?$nombre["width"]:'100'.'px;';
				$color= (!empty($nombre["bordercolor"]))?$nombre["bordercolor"]:'#c0c0c0';
				$stcel= ($nombre["separator"]==1)?"border-bottom-style:solid;border-bottom-width:{$nombre['bordersize']}px;border-bottom-color:{$color};":"";
				$bkimg= (!empty($nombre["bgimage"]))?"background:url({$nombre['bgimage']}) repeat-y;":'';
				$retStr.= "<div id='{$nombre['MM_DivName']}' class='MM_menudiv' style=\"width:{$width}px;z-index:10000;visibility:hidden;display:block;position:absolute;border-color:{$color};".
					$stylo.'" onmouseover="javascript:MM_om=true;" onmouseout="javascript:MM_om=false;">'.$this->br.
					"<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" style=\"".$this->fontStyle."\">".$this->br;
				foreach ($this->DIVdata as $items){
					if ($items["MM_DivName"] == $nombre["MM_DivName"]){
						if( $items["displaytext"] == "SEPARATOR" && $items["link"] == "SEPARATOR" && $items["image"] == "SEPARATOR" ){
/*							if ( $nombre["useimages"] == 0 ){
								$retStr.= '<tr>'.$this->br.'<td width="0" height="1" style="'.$stcel.$bkimg.'" valign="middle" align="center">'.$this->br.'</td>'.$this->br;
							}else{
								$retStr.= '<tr>'.$this->br.'<td width="22" height="1" style="'.$stcel.$bkimg.'" valign="middle" align="center">'.$this->br.'</td>'.$this->br;
							};
*/							$retStr.= '<tr><td colspan="2" height="'.$items['style'][0].'" style="padding-left:3px">'.$this->br.'<table border="0" cellspacing="0" cellpadding="0" style="width:100%;height:100%;">'.$this->br.'<tr>'.$this->br.'<td width="100%" height="100%" style="background-color:'.eval($items['style'][1]).'">'.$this->br.'</td>'.$this->br.'</tr>'.$this->br.'</table>'.$this->br.'</td>'.$this->br.'</tr>';
						}else{
							$retStr.= '<tr style="height:'.$nombre["height"].'px;cursor:pointer;" onMouseOver="this.style.backgroundColor=\''.$nombre["mouseovercolor"].'\'" onMouseOut="this.style.backgroundColor=\''.$nombre["backgcolor"].'\'" onclick=';
							$cjh= strtoupper($items["link"]);
							if (strpos( $cjh, 'JAVASCRIPT:') === false){
								$retStr.= "MM_process('".$items["link"]."','H')";
							}else{
								$cjh=str_replace("javascript:","",$items["link"]);
//								$cjh=str_replace("'","\'",$cjh);
//								$cjh=str_replace('"','\\"',$cjh);
								$retStr.= "MM_process('".$cjh."','J')";
							};
							$retStr.= '>'.$this->br;
							if ($nombre["useimages"]==0){
								$retStr.= '<td width="0" style="'.$stcel.$bkimg.'; border-right:1px solid #E2E3E3" valign="middle" align="center">'.$this->br;
							}else{
								$retStr.= '<td width="22" style="'.$stcel.$bkimg.'; border-right:1px solid #E2E3E3" valign="middle" align="center">'.$this->br;
								if ($nombre["useimages"] == 1 && $items["image"] != ''){ $retStr.= '<img border="0" src="'.$items["image"].'" alt="'.$items["displaytext"].'" />';}else{$retStr.= "&nbsp;";};
							};
							$retStr.= '</td>'.$this->br.'<td style="padding:5px 0 5px 3px;'.$stcel.'; border-left:1px solid #fff" valign="middle" align="left">'.$items["displaytext"].'</td>'.$this->br.'</tr>'.$this->br;
						};
					};/*if is part of the same div*/
				};/*foreach */
				$retStr.= "</table>".$this->br."</div>".$this->br;
			}
			if (isset($this->retcode))
				return $retStr;
			else
				echo $retStr;
		}
	}/*class*/
?>