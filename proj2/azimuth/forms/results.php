<?php

/*Pre-Load Section - NO HTML Output******************************************/

//Pre-Load Variable Definition
$ROOT = '../';

session_start();

$servername = "studentdb-maria.gl.umbc.edu";
$username = "XX";
$password = "catdogdonutman27";
$stuidCopy = $_SESSION['stuid'];

// Set up link to database server
$link = mysql_connect($servername, $username, $password);
if (!$link) {
  die("Could not connect: " . mysql_error());
}

// Select proper database
$db_selected = mysql_select_db('XX', $link);
if (!$db_selected) {
  die ('Can\'t use XX : ' . mysql_error());
}



if(isset($_POST["reset"])) {

  mysql_query("DELETE FROM StudentInfo WHERE UMBCID = '$stuidCopy'");
  mysql_query("DELETE FROM Suggestions WHERE UMBCID = '$stuidCopy'");
  
  session_destroy();
  header('Location: ../get-started.php');
}

/*Load Section - HTML Output OK**********************************************/

//Output HTML Azimuth Header
include('../header.php');

//Output HTML Azimuth Article
include('html/results.html');

// Retrieve the total number of courses from the Courses table
$result = mysql_query("SELECT COUNT(*) FROM Courses");
if(!$result){
  die('Could not query: ' . mysql_error());
}
$count = mysql_result($result,0);

// Retrieve a list of all the courses
$courseList = mysql_query("SELECT Course FROM Courses");
if(!$courseList){
  die('Could not query: ' . mysql_error());
}
if(!isset($_POST["reset"])){
// Store the student's info into the StudentInfo table
$result = mysql_query("INSERT INTO StudentInfo (StudentName, UMBCID, Email) VALUES ('".$_SESSION['name']."', '".$_SESSION['stuid']."', '".$_SESSION['email']."')");

// Output student's information
$studentName = $_SESSION['name'];
$idOfStudent = $_SESSION['stuid'];
$studentEmail = $_SESSION['email'];
echo("<fieldset><legend><strong>Student Information</strong></legend><div class=\"table\">");
echo("<div class=\"row\"><div class=\"course\"><strong>Name:</strong> $studentName </div></div>");
echo("<div class=\"row\"><div class=\"course\"><strong>UMBC ID:</strong> $idOfStudent </div></div>");
echo("<div class=\"row\"><div class=\"course\"><strong>Email:</strong> $studentEmail </div></div></div></fieldset>");
if(!$result){
  die('Could not query: ' . mysql_error());
}

// Also store their info in the Suggestions table
$result = mysql_query("INSERT INTO Suggestions (StudentName, UMBCID, Email) VALUES ('".$_SESSION['name']."', '".$_SESSION['stuid']."', '".$_SESSION['email']."')");

if(!$result){
  die('Could not query: ' . mysql_error());
}

// Retrieve student's UMBC ID from SESSION
$studentId = $_SESSION["stuid"];

// Loop through every single course
for($i=0; $i<$count; $i++){
  // The course number is the current index + 1
  $courseNumber = $i+1;
  // If the student selected that they have taken the current course
  if(strcmp($_POST["$courseNumber"], "1") == 0){
	// Then update their info in the StudentInfo table to record that they have taken it
    $result = mysql_query("UPDATE StudentInfo SET `$courseNumber`='1' WHERE UMBCID='$studentId'");
    if(!$result){
      die('Could not query: ' . mysql_error());
    }
  }
}

// Obtain the number of prerequisites for each course
$prereqStr = mysql_query("SELECT Prerequisites FROM Courses");
if(!$prereqStr){
  die('Could not query: ' . mysql_error());
}

// Loop through every course
for($i=0; $i<$count; $i++){
  $currentCourse= $i + 1;
  $canTakeCourse = True;
  // Soecial case for CMSC 447 since it requires any CMSC 4xx level as a prereq
  $canTake447 = False;
  
  /* If the user has already taken and passed the course, they should not take it again
   SESSION fetches what the user submitted for the current course's checkbox. 
   If the user selected that they have taken a course, its value in SESSION will be 1
   otherwise it will be 0 if they haven't taken it.
   */
  if((strcmp($_POST["$currentCourse"],"1")) == "0"){
    $canTakeCourse = False;
  }
  
  // If they have not taken the current course, check to see if they are eligible
  // to take it
  else{
	// Get the number of prerequisites for the current course from prereqStr using
	// mysql_result
    $numPrereqs = mysql_result($prereqStr, $i);
	
	// If the course has no prerequisites, then the user can take it
    if(strcmp($numPrereqs, "0") == "0"){
		
	  // Here we code for the special case of CMSC 447 because of its unique requirement
      if($currentCourse == 29){
		// Indices 13-57 are all CMSC 400 level courses so if they have passed any of those
		// courses, then the user can take CMSC 447
		for($index=13; $index<57; $index++){
		  if(strcmp($_POST["$index"], "1") == "0"){
	        $canTake447 = True;
		  }
	    }
      }
    }
	// For all other non CMSC 447 courses
    else{
	  // Loop through the number of prereqs for the current course to check if the user has
	  // passed all of the prerequisite courses
      for($j=0; $j<$numPrereqs; $j++){
	    $jNum = $j + 1;
		// Obtain the course ID of the current prerequisite so we know what course to check
		$prereqNeeded = mysql_query("SELECT prereq$jNum FROM Courses WHERE ID='$currentCourse'");
		if(!$prereqNeeded){
		  die('Could not query: ' . mysql_error());
		}
		// Fetch the prereq ID using mysql_result()
		$courseNeeded = mysql_result($prereqNeeded, 0);
		// Obtain boolean value 0 or 1 stating whether or not the student has taken the prereq course
		$takenCourse = mysql_query("SELECT `$courseNeeded` FROM StudentInfo WHERE UMBCID='$studentId'");
		if(!$takenCourse){
		  die('Could not query: ' . mysql_error());
		}
		// Obtain value using mysql_result
		$hasTakenCourse = mysql_result($takenCourse,0);
		// If the user has taken the prereq course, do nothing for now. We must check and make sure they
		// have taken all of the prerequisites
		if($hasTakenCourse == 1){

		}
		// If the student has not taken any number of the prerequisites, they can't take the current course
		else{
		  $canTakeCourse = False;
		}
       }

    }
  }

  // Special case checking if the user can take CMSC 447, 29 is the ID of CMSC 447
  // in the Courses table in the database
  if($currentCourse == 29 && $canTake447==True){
	// Update the Suggestions database stating the user can take the current course
    $updateStr = mysql_query("UPDATE Suggestions SET `$currentCourse`='1' WHERE UMBCID='$studentId'");
    if(!$updateStr){
      die('Could not query: ' . mysql_error());
    }
  }
  
  else{
	// Do nothing for CMSC 447 since we already checked it
    if($currentCourse == 29){

    }
    else{
	  // If the user can take the current course
      if($canTakeCourse == True){
		// Update their info in the Suggestions table stating that they can take the current course
        $updateStr = mysql_query("UPDATE Suggestions SET `$currentCourse`='1' WHERE UMBCID='$studentId'");
        if(!$updateStr){
          die('Could not query: ' . mysql_error());
        }
      }
    }
  }
}//...this was supposed to be a simple EXCEPT query Oneill. wtf

// Student Input Confirmation Output



// Results Output


echo("<fieldset><legend><strong>Computer Science Courses</strong></legend><div class=\"table\">");


for($i=0; $i<$count; $i++){
  $coursePlusOne = $i+1;
  // Obtain the current course the user can take to display
  $thisCourse = mysql_query("SELECT `$coursePlusOne` FROM Suggestions WHERE UMBCID='$studentId'");
  if(!$thisCourse){
    die('Could not query: ' . mysql_error());
  }

  // Output a new table once we reach the Science courses
  if($coursePlusOne == 57){
    echo("<fieldset><legend><strong>Science Elective Courses</strong></legend><div class=\"table\">");
  }
  // Output a new table once we reach the Math courses
  else if($coursePlusOne == 69){
    echo("<fieldset><legend><strong>Math Courses</strong></legend><div class=\"table\">");
  }

  // Fetch if the user can take the current course
  $canTake = mysql_result($thisCourse, 0);
  // If the user can take the current course, display it in the output table
  if(strcmp($canTake, "1") == "0"){
    $courseOutput = mysql_result($courseList, $i);
    echo("<div class=\"row\"><div class=\"course\">".$courseOutput."</div></div>");
  }
  // End the table at the end of each course section
  if($coursePlusOne == 56 || $coursePlusOne == 68 || $coursePlusOne == 72){
    echo("</div></fieldset>");
  }
}
}

?>

  <p>

  </p>

</div>

<?php
// Close connection to database
mysql_close($link);


//Output HTML Azimuth Footer
include('../footer.php');


?>