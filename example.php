<?php
/**
example.php - A script for testing the ColorChip PHP class

Copyright (C) 2002  Andy Chase
<achase@greyledge.net>
http://andy.greyledge.net

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
******************************************************************************/

/* Include the class definition; make sure the class file
 * is in your php_ini path 
 */
require_once("ColorChip.class.php");


/*****************************************************************************/
//Define some functions

function makeForm($currentColor = ''){
  echo <<<END
<a name="form"/><h2>Enter a New Color</h2>
<form action="example.php" method="get">
   Enter a Hex color: #<input type="text" size="7" maxlength="6" name="userColor" value='$currentColor'/><br/>
    <em style="font-size:smaller">(For example, #336699)</em><br/>
<input type="submit"/>
<input type="hidden" name="action" value="go"/>
</form>

END;
}

function dumpProperties($colorObj){
  $hex = $colorObj->hex;

  $linkColor = ($colorObj->v > 60 ? "000000" : "FFFFFF");
  echo "Hex: #<a style='color:#$linkColor' href='example.php?userColor=$hex&action=go'>" . $colorObj->hex . "</a><br/>";
  echo "RGB: " . implode(", ", array($colorObj->r, $colorObj->g, $colorObj->b)) . "<br/>";
  echo "HSV: " . implode(", ", array($colorObj->h, $colorObj->s, $colorObj->v)) . "<br/>";

}

function makeColorDiv($colorObj){

  echo "<div style='background-color:#$colorObj->hex;padding:.5em'>";
  echo "<strong style='color:#FFFFFF'>";
  dumpProperties($colorObj);
  echo "</strong><br/>";
  echo "<strong style='color:#000000'>";
  dumpProperties($colorObj);
  echo "</strong>";
  echo "</div>\n";

}
/*****************************************************************************/


/*****************************************************************************/
// Open the HTML page
echo <<<END
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>ColorChip - Easy Color Manipulation with PHP</title>
<style type="text/css">
div{
  width:auto;
}
body{
  background-color:#999999;
 color:#000000;
  font-family:arial,helvetica,sans-serif;
  font-size:9pt;
}
a{
 color:#000066;
}
    </style>
  </head>
    <body>
    <h1>Easy Color Manipulation with PHP</h1>
    <p>Powered by <a href="http://andy.greyledge.net/ColorChip/">ColorChip</a></p>

<hr/>
END;

/*****************************************************************************/
//Begin main script.


//If the GET variable '$action' is set to 'go', do color calculations.  Otherwise, simply display the form:

if($_GET['action'] == 'go'){

  //Show menu
echo <<<END
    <p>To switch to a new color, click on the hex value of any color below, or enter a new one of your own at the bottom of the page.</p>

<ul>
    <li><a href='#websafe'>Nearest Web Safe</a></li>
    <li><a href='#complement'>Complementary Color</a></li>
    <li><a href='#triad'>Triad Colors</a></li>
    <li><a href='#lighttodark'>Light to Dark</a></li>
    <li><a href='#hues'>Nearby Hues</a></li>
    <li><a href='#sat'>Saturation Range</a></li>
    <li><a href='#form'>Enter a New Color</a></li>
</ul>

END;

  $userColor = strtoupper($_GET['userColor']);
  $color = new ColorChip($userColor, null, null, CC_HEX);
  $complement = $color->getComplementary();
  $triad = $color->getTriad();

  echo "<h2>Original Color:</h2>";
  makeColorDiv($color);

  //Show the nearest web safe color
  $webSafe = $color->getNearestWebSafe();
  echo "<a name='websafe'/>\n<h2>Nearest Web Safe</h2>";
  makeColorDiv($webSafe);

  //Show complementary color:
  echo "<a name='complement'/>\n<h2>Complementary Color:</h2>";
  makeColorDiv($complement);


  //Show color triad:
  echo "<a name='triad'/>\n<h2>Triad</h2>";
  echo "<table>\n<tr>\n";

  echo "<td>";
  makeColorDiv($color);
  echo "</td>";
  echo "<td>";
  makeColorDiv($triad[0]);
  echo "</td>";
  echo "<td>";
  makeColorDiv($triad[1]);
  echo "</td>";

  echo "</tr>\n</table>\n";


  //Show range of light to dark in selected color's hue/saturation
  $lightDark = new ColorChip($color->h, $color->s, 100, CC_HSV);
  echo "<a name='lighttodark'/>\n<h2>Light to Dark:</h2>";
  echo "<table>\n<tr>\n";
  for($x = 1; $x <= 8; $x++){
    echo "<td style='width:auto'>\n";
    makeColorDiv($lightDark);
    echo "</td>\n";
    $lightDark->adjValue(-14);
  }
  echo "</tr>\n</table>\n";



  //Show range of nearby shades
  $hues = $color->clone();
  $hues->adjHue(-30);
  echo "<a name='hues'/>\n<h2>Nearby Hues</h2>";
  echo "<table>\n<tr>\n";
  for($x = 1; $x <= 7; $x++){
    echo "<td style='width:auto'>";
    makeColorDiv($hues);
    $hues->adjHue(10);
    echo "</td>\n";
  }
  echo "</tr>\n</table>\n";


  //Show range of saturation
  $sat = new ColorChip($color->h, 0, $color->v, CC_HSV);
  echo "<a name='sat'/>\n<h2>Saturation Range</h2>";
  echo "<table>\n<tr>\n";
  for($x = 1; $x<=8; $x++){
    echo "<td style='width:auto'>";
    makeColorDiv($sat);
    $sat->adjSaturation(15);
    echo "</td>\n";
  }
  echo "</tr>\n</table>\n";

  //Add the form to the bottom of the page.
  echo "<hr/>";
  makeForm($color->hex);


}else{

  makeForm();
}

/*****************************************************************************/
//Close HTML page

echo <<<END
</body>
</html>

END;
/*****************************************************************************/

?>