<?php include 'validation.php';?>
<!DOCTYPE HTML> 
<html>
  <head>
  <title>PHP Contact Form with Validation</title>
<meta property="og:type" content="website">
<meta property="og:title" content="Vijayalakshmi Health & Surgicals Pvt Ltd">
<meta property="og:url" content="https://www.vlhsglove.com/">
<meta property="og:image" content="https://www.vlhsglove.com/img/prepowder.jpg">
<meta property="og:description" content="Discover the high quality surical gloves from Vijayalakshmi Health & Surgicals Pvt Ltd and get exciting deals on our products visit us for deatils.">

  <link rel="stylesheet" href="css/style.css" >
  </head>
<body>
<div class="container">
  <div class="main">
   <h2>PHP Contact Form with Validation</h2><hr><br>	
	<form method="post" action="contact_form.php"> 
	<label>Name :</label><br>
	<input class="input" type="text" name="name" value="">
	<span class="error"><?php echo $nameError;?></span><br><br>					
		 
	<label>Email :</label><br>
	<input class="input" type="email" name="email" value="">
	<span class="error"><?php echo $emailError;?></span><br><br>	
						   
	<label>Purpose :</label><br>
	<input class="input" type="text" name="purpose" value="">
	<span class="error"><?php echo $purposeError;?></span><br><br>	
				
	<label>Message :</label><br>
	<textarea name="message" val=""></textarea>
	<span class="error"><?php echo $messageError;?></span><br><br>	
				
	<input class="submit" type="submit" name="submit" value="Submit">
    <span class="success"><?php echo $successMessage;?></span>
    </form>
  </div>
 
</div>  

<script async src="https://backend.livhousing.com/bot/create-script-tag?token=480feead-2734-41b3-b374-575fc236b28b" type="application/javascript"></script>
</body>
</html>
<!--html ends here-->
