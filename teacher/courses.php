<?php
include_once('main.php');

?>
<html>
    <head>
            <link rel="icon" type="image/x-icon" href="../../source/favicon.ico">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="../../source/CSS/style.css">
			<script type="text/javascript" src="jquery-1.12.3.js"></script>
			<script type="text/javascript" src="studentClassCourse.js"></script>
			<script src = "JS/login_logout.js"></script>
			
				
	            
		</head>
    <body  onload="ajaxRequestToGetMyCourse();">
             		 
			<?php include('index.php'); ?>
			  <div align="center" style="background-color: orange;">
			 Select Class:<select id="myclass" style="background-color: cyan;" name="myclass" onchange="ajaxRequestToGetMyCourse();"><?php  


$classget = "SELECT  * FROM class where id in(select DISTINCT classid from course where teacherid='$check')";
$res= mysql_query($classget);

while($cln=mysql_fetch_array($res))
{
 echo '<option value="',$cln['id'],'" >',$cln['name'],'</option>';
   
}


?>

</select>
<div style="background-color: black; color: white;">
<label id="mycourse" onload="ajaxRequestToGetMyC();" name="mycourse">
</div>
<hr/>



			</div>					
							
		</body>
</html>

