<?php
include_once('main.php');
include_once('../../service/mysqlcon.php');
$sql = "SELECT * FROM students;";
$res= mysql_query($sql);
$string = "";
$images_dir = "../images/";
while($row = mysql_fetch_array($res)){
    $picname = $row['id'];
    $string .= '<tr><td>'.$row['id'].'</td><td>'.$row['name'].
    '</td><td>'.$row['phone'].'</td><td>'.$row['email'].
    '</td><td>'.$row['sex'].'</td><td>'.$row['dob'].
    '</td><td>'.$row['addmissiondate'].'</td><td>'.$row['address'].
    '</td><td>'.$row['parrentid'].'</td><td>'.$row['classid'].
    "</td><td><img src='".$images_dir.$picname.".jpg' alt='$picname' width='150' height='150'>".'</td></tr>';
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Students</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="icon" type="image/x-icon" href="../../source/favicon.ico">
    <script src = "JS/login_logout.js"></script>
    <script src = "JS/searchStudent.js"></script>
		</head>
    <body>
			  <div class="header"><h1>MIU SCIENCE FACULTY PORTAL</h1></div>
			  <div class="divtopcorner">
				    <img src="../source/miu/img.png" height="150" width="150" alt="MIU SCIENCE FACULTY PORTAL"/>
				</div>
			<br/><br/>
				<ul>
				    <li class="manulist">
						    <a class ="menulista" href="index.php">Home</a>
								<a class ="menulista" href="manageStudent.php">Manage Student</a>
								<a class ="menulista" href="index.php">Manage Teacher</a>
								<a class ="menulista" href="index.php">Manage Parent</a>
								<a class ="menulista" href="index.php">Manage Staff</a>
								<a class ="menulista" href="index.php">Course</a>
								<a class ="menulista" href="index.php">Attendance</a>
								<a class ="menulista" href="index.php">Exam Schedule</a>
								<a class ="menulista" href="index.php">Salary</a>
								<a class ="menulista" href="index.php">Report</a>
								<a class ="menulista" href="index.php">Payment</a>
								<div align="center">
								<h4>Hi!admin <?php echo $check." ";?></h4>
								<a class ="menulista" href="logout.php" onmouseover="changemouseover(this);" onmouseout="changemouseout(this,'<?php echo ucfirst($loged_user_name);?>');"><?php echo "Logout";?></a>
						</div>
								</li>
				</ul>
			  <hr/>
        <center>
            <table>
                <tr>
                    <td><b>Search By Id Or Name OR Class Id: </b></td>
                    <td><input type="text" name="searchId" placeholder="Search By Id Or Name:" onkeyup="getStudent(this.value);"></td>
                </tr>
            </table>
        </center>
        <br/>
        <center><h2>Students List</h2></center>
        <center>
            <table border="1" id='studentList'>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>Addmission Date</th>
                    <th>Address</th>
                    <th>Parent Id</th>
                    <th>Class Id</th>
                    <th>Picture</th>
                </tr>
                <?php echo $string;?>
            </table>
        </center>
		</body>
</html>
