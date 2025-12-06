<?php
include_once('main.php');
$count=0;
$st=mysql_query("SELECT *  FROM teachers WHERE id='$check' ");
$stinfo=mysql_fetch_array($st);

$attendmon = "SELECT DISTINCT(date) FROM attendance WHERE attendedid='$check' and  MONTH( DATE ) = MONTH( CURRENT_DATE ) and YEAR( DATE )=YEAR( CURRENT_DATE )";
$resmon = mysql_query($attendmon);

while($r=mysql_fetch_array($resmon))
{
 $count+=1;
}
?>
<html>
    <head>
		    
			<script type="text/javascript" src="jquery-1.12.3.js"></script>
			<script type="text/javascript" src="Attendance.js"></script>
			 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
		     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
             <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
             <link rel="stylesheet" href="../../source/CSS/style.css">
				<script src = "JS/login_logout.js"></script>
				<script src = "JS/modifyValidate.js"></script>
		</head>
		<style>
		input {
    text-align: center;
    background-color: gray;
           }
		
		</style>
    <body>
             		 
		
			  				<?php include('index.php'); ?>
						</div>
						 
				    
			
						</li>
				</ul>
			 
			    <div align="center">
			  	<h1 style="background-color:black; color:white;">My Salary</h1>
				<hr/>
			  <table border="1">
			  <tr>
			  <th>Teacher Monthly Salary</th>
			 <th>Teacher Payable Salary This Month</th>
			   </tr>
			  <tr>
			  <td><?php echo round($stinfo['salary']/12,2);?></td>
			 <td><?php echo round(($stinfo['salary']/300)*$count,2);?></td>
			  </tr>
			  
			  
			  <table
								
								</div>
			<hr/>
		</body>
</html>

