<?php

/*Pre-Load Section - NO HTML Output******************************************/

//Pre-Load Variable Definition
$ROOT = '../';

session_start();

session_start();
session_register("courses_valid");
settype($_SESSION["courses_valid"], "bool");

$_SESSION["courses_valid"] = False;

//Initialize Database Access
$servername = "studentdb-maria.gl.umbc.edu"; 
$username = "XX"; 
$password = "catdogdonutman27"; 
$link = mysql_connect($servername, $username, $password); 

//Database Connect Error
if (!$link) {
  die("Could not connect: " . mysql_error());
}

//Table Select Error
$db_selected = mysql_select_db('XX', $link);
if (!$db_selected) {
  die ('Can\'t use XX : ' . mysql_error());
}

/*Output Section - HTML Output OK********************************************/ 

//Output HTML Azimuth Header 
include('../header.php');

//Output HTML Azimuth Article 
include('html/courses.html'); 

/*Output Degree Core Course List**********************************************/

//Query Course List for Core (SELECT order corresponds to $row index)
$result = mysql_query("SELECT ID, Subject, Course, Credits, Required, Description FROM Courses WHERE Subject = 'CMSC'");
if(!$result) {
  die('Could not query: ' . mysql_error());
}
// Keep track of number of courses
$courseCount = 0; //wtf Joe. god help us if the db changes
//Core Course List Head
echo("<form action=\"results.php\" method=\"post\"><fieldset><legend><strong>Computer Science Courses</strong></legend><div class=\"table\">");
while($row = mysql_fetch_row($result)) {

  if($row[4] == 0) {
    $row[4] = "No";
  }
  else {
    $row[4] = "Yes";
  }
  //Output Checkbox
  $courseCount++;
  echo("<div class=\"row\"><div class=\"box\"><input type=\"checkbox\" name=\"$courseCount\" value=\"1\"".$row[0]."\"></div>");
  //Output Course Data
  echo("<div class=\"course\">" .$row[2]. "<div class=\"dropdown\"><strong>Course Description:</strong><br /><p>".$row[5]."</p><strong>Credits:</strong> ".$row[3]." <strong>Required:</strong> ".$row[4]."</div></div></div>");
}
echo("</div></fieldset>");

/*Output Degree Elective Course List******************************************/

//Query Course List for Electives (SELECT order corresponds to $row index)
$result = mysql_query("SELECT ID, Subject, Course, Credits, Required, Description FROM Courses WHERE Subject != 'CMSC' AND Subject != 'MATH' AND Subject != 'STAT'");
if(!$result) {
  die('Could not query: ' . mysql_error());
}

//Elective Course List Head
echo("<fieldset><legend><strong>Science Elective Courses</strong></legend><div class=\"table\">");
while($row = mysql_fetch_row($result)) {

  if($row[4] == 0) {
    $row[4] = "No";
  }
  else {
    $row[4] = "Yes";
  }
  //Output Checkbox
  $courseCount++;
  echo("<div class=\"row\"><div class=\"box\"><input type=\"checkbox\" name=\"$courseCount\" value=\"1\"".$row[0]."\"></div>");
  //Output Course Data
  echo("<div class=\"course\">" .$row[2]. "<div class=\"dropdown\"><strong>Course Description:</strong><br /><p>".$row[5]."</p><strong>Credits:</strong> ".$row[3]." <strong>Required:</strong> ".$row[4]."</div></div></div>");
}
echo("</div></fieldset>");

/*Output Degree Mathematics Course List***************************************/

//Query Course List for Mathematics (SELECT order corresponds to $row index)
$result = mysql_query("SELECT ID, Subject, Course, Credits, Required, Description FROM Courses WHERE Subject = 'MATH' OR Subject = 'STAT'");
if(!$result) {
  die('Could not query: ' . mysql_error());
}

//Mathematics Course List Head
echo("<fieldset><legend><strong>Mathematics Courses</strong></legend><div class=\"table\">");
while($row = mysql_fetch_row($result)) {

  if($row[0] == 0) {
    $row[0] = "No";
  }
  else {
    $row[0] = "Yes";
  }
  //Output Checkbox
  $courseCount++;
  echo("<div class=\"row\"><div class=\"box\"><input type=\"checkbox\" name=\"$courseCount\" value=\"1\"".$row[0]."\"></div>");
  //Output Course Data
  echo("<div class=\"course\">" .$row[2]. "<div class=\"dropdown\"><strong>Course Description:</strong><br /><p>".$row[5]."</p><strong>Credits:</strong> ".$row[3]." <strong>Required:</strong> ".$row[4]."</div></div></div>");

}
echo("</div></fieldset>");


//Checkboxes Pre-Validated
$_SESSION["courses_valid"] = True;

//Output Form Submit
echo("<input type=\"submit\" value=\"Submit\">"); 

//Form and Article Close Out
echo("</form></div>");  

//Output HTML Azimuth Footer
include('../footer.php');

//Session and Database Close Out
mysql_close($link);

?>
