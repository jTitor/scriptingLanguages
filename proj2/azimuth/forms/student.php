<?php

/*Pre-Load Section - NO HTML Output******************************************/

//Pre-Load Variable Definition
$ROOT = '../';

session_start();
session_register("student_valid");
settype($_SESSION["student_valid"], "bool");

$_SESSION["student_valid"] = False;

/*Page Script Section - NO HTML Output***************************************/

//Script Variable Definition
$name = $stuid = $email = $degree = "";
$nameErr = $stuidErr = $emailErr = $degreeErr = "";
$checksum = 0;

settype($checksum, "integer");

//clean_input - Sanitizes string inputs for potential code injection
function clean_input($data) {
  $data = strip_tags($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

//Check for Redirect
if($_SESSION["student_valid"] == True) {
  if(isset($_SESSION["courses_valid"]) &&
     $_SESSION["courses_valid"] == True){
    header('Location: forms/results.php');
  }
  else {
    header('Location: forms/courses.php');
  }
}

//Student Information Form Validation
if($_SERVER["REQUEST_METHOD"] == "POST") {

  //Validate Name
  if(empty($_POST["name"])) {
    $nameErr = "Name is Required";
  }
  else {
    $name = rtrim(clean_input($_POST["name"]));
    //Check Letters and non-trailing Whitespace Only
    if(!preg_match("/^[a-zA-Z ]*$/",$name)) {
      $nameErr = "Letters and spaces only";
    }
    else {
      $checksum++;
    }
  }
  //Validate Campus ID
  if(empty($_POST["stuid"])) {
    $stuidErr = "Campus ID is Required";
  }
  else {
    $stuid = trim(clean_input($_POST["stuid"]));
    //Check UMBC ID Pattern Match
    if(!preg_match("/^[a-zA-Z]{2}[0-9]{5}$/",$stuid)) {
      $stuidErr = "Invalid Campus ID format";
    }
    else {
      $checksum++;
    }
  }
  //Validate UMBC Email
  if(empty($_POST["email"])) {
    $emailErr = "UMBC Email is Required";
  }
  else {
    $email = trim(clean_input($_POST["email"]));
    //Check UMBC Email Pattern Match
    if(!preg_match("/^[a-z0-9]{3,}+@umbc.edu$/",$email)) {
      $emailErr = "Invalid UMBC Email format";
    }
    else {
      $checksum++;
    }
  }
  //Validate Degree
  if(($_POST["degree"]) == "") {
    $degreeErr = "Degree is Required";
  }
  else {
    $degree = $_POST["degree"];
    $checksum++;
  }
  
  //Check All Fields Valid
  if($checksum == 4) {
    
    //Register and Set Session Variables
    session_register("name");
    session_register("stuid");
    session_register("email");
    session_register("degree");
    
    $_SESSION["name"] = $name;
    $_SESSION["stuid"] = $stuid;
    $_SESSION["email"] = $email;
    $_SESSION["degree"] = $degree;
    $_SESSION["student_valid"] = TRUE;
    
    //Redirect to Next Form
    header('Location: courses.php');
  }
}

/*Output Section - HTML Output OK*********************************************/

//Output HTML Azimuth Header
include('../header.php');

//Output HTML Azimuth Article
include('html/student.html');

//Output HTML Student Info Form
?>
  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" 
  method="post">
    <fieldset>
      <legend><strong>UMBC Student Information</strong></legend>
      <label for="name">Name</label><br />
      <input type="text" name="name" id="name" value="<?php echo $name;?>" 
  maxlength="70" required>
      <div id="nameErr"></div><br />
      <label for="stuid">Campus ID</label><br />
      <input type="text" name="stuid" id="stuid" value="<?php echo $stuid;?>" 
  maxlength="7" size="10" required>
      <div id="stuidErr"></div><br />
      <label for="email">UMBC Email</label><br />
      <input type="email" name="email" id="email" value="<?php echo $email;?>" 
  maxlegnth="40" required>
      <div id="emailErr"></div><br />
      <label for="degree">Degree</label><br />
      <select name="degree" id="degree"required>
        <option value="" disabled selected value></option>
        <option value="cmsc">Computer Science</option>
      </select><div id="degreeErr"></div><br />
      <input type="submit" id="submit" value="Submit">
    </fieldset>
  </form>
</div>

<script src="https://code.jquery.com/jquery-1.10.2.js"></script>

<script>

//name is valid
var nIsValid = true; 

//campus id is valid
var cIsValid = true;

//email is valid
var eIsValid = true;

//degree is valid
var dIsValid = true;


//validate name
$("#name").change(function () {
  var nameErr;
  if ($(this).val() == "") {
    nameErr = "Name is Required";
    nIsValid = false;
  }
  else
  {
    if( !(/^[a-zA-Z ]*$/.test($(this).val())) )
    {
      nameErr = "Letters and spaces only";
      nIsValid = false;
    }
    else
    {
    	nIsValid = true;
    	$("#nameErr").html("");
    }
  }
  if(!nIsValid)
  {
  	$("#nameErr").css("color", "red"); 
    $("#nameErr").html(nameErr);
  }

  if( !(nIsValid && cIsValid && eIsValid && dIsValid) )
  {
  	$("#submit").prop("disabled",true);
  }
  else
  {
  	$("#submit").prop("disabled",false);
  }
}).change();


//validate campus id
$("#stuid").change(function () {
  var stuidErr;
  if ($(this).val() == "") {
    stuidErr = "Campus ID is Required";
    cIsValid = false;
  }
  else
  {
    if( !(/^[a-zA-Z]{2}[0-9]{5}$/.test($(this).val())) )
    {
      stuidErr = "Invalid Campus ID format";
      cIsValid = false;
    }
    else
    {
    	cIsValid = true;
    	$("#stuidErr").html("");
    }
  }
  if(!cIsValid)
  {
  	$("#stuidErr").css("color", "red"); 
    $("#stuidErr").html(stuidErr);
  }

  if( !(nIsValid && cIsValid && eIsValid && dIsValid) )
  {
  	$("#submit").prop("disabled",true);
  }
  else
  {
  	$("#submit").prop("disabled",false);
  }
}).change();


//validate email
$("#email").change(function () {
  var emailErr;
  if ($(this).val() == "") {
    emailErr = "UMBC Email is Required";
    eIsValid = false;
  }
  else
  {
    if( !(/^[a-z0-9]{3,}@umbc\.edu$/.test($(this).val())) )
    {
      emailErr = "Invalid UMBC Email format";
      eIsValid = false;
    }
    else
    {
    	eIsValid = true;
    	$("#emailErr").html("");
    }
  }
  if(!eIsValid)
  {
  	$("#emailErr").css("color", "red"); 
    $("#emailErr").html(emailErr);
  }

  if( !(nIsValid && cIsValid && eIsValid && dIsValid) )
  {
  	$("#submit").prop("disabled",true);
  }
  else
  {
  	$("#submit").prop("disabled",false);
  }
}).change();


//validate degree
$("#degree").change(function () {
  var degreeErr;
  if ( $(this).val() != "cmsc") {
    degreeErr = "Degree is Required";
    dIsValid = false;
  }
  else
  { 
    dIsValid = true;
    $("#degreeErr").html("");
  }
  if(!dIsValid)
  {
  	$("#degreeErr").css("color", "red"); 
    $("#degreeErr").html(degreeErr);
  }

  if( !(nIsValid && cIsValid && eIsValid && dIsValid) )
  {
  	$("#submit").prop("disabled",true);
  }
  else
  {
  	$("#submit").prop("disabled",false);
  }

}).change();

</script>

<?php

//Output HTML Azimuth Footer
include('../footer.php');

?>