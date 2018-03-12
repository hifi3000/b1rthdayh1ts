<!DOCTYPE html>
<html>
<head>
	<title>B1RTHDAY H1TS</title>
	<meta http-equiv="Content-Type" content="text/html"><meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="/media/attack100_icon.png">
	<link href='https://fonts.googleapis.com/css?family=Basic' rel='stylesheet'>
	<link href='https://fonts.googleapis.com/css?family=Caveat' rel='stylesheet'>
	<link href='https://fonts.googleapis.com/css?family=ABeeZee' rel='stylesheet'>
	<link rel="stylesheet" type="text/css" href="css/mystyle.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="js/selecttable.js"></script>
</head>
<?php
	include 'php/db.php';
?>
<body>
<div class="header">
<p><div class="big"><a href='http://b1rthdayh1ts.attack100.de'>B1RTHDAY H1TS</a></div><br>What was number one on each of your birthdays?
</div>

<div class="navbar">
<div class="navbar_left">
	<a href='http://b1rthdayh1ts.attack100.de'>HOME</a>
</div>
<div class="navbar_center">
	<form method="get" action="index.php?">
		<label for="birthday">YOUR BIRTHDAY:</label>
		<?php 
		if(isset($_GET[go01])) {
			$birthday=$_GET[birthday];
			if (preg_match("#^[0-9]{2}.[0-9]{2}.[0-9]{4}$#", $birthday)){
				echo "<input type='text' id='birthday' name='birthday' value=\"$birthday\" autofocus>";
			}
			else echo "<input type='text' id='birthday' name='birthday' placeholder='i.e. 21.02.1984' autofocus>";
		}
		else echo "<input type='text' id='birthday' name='birthday' placeholder='21.02.1984' autofocus>";
		?>
		<input type="submit" name="go01" value="&#xf002;">
	</form>
</div>
<div class="navbar_right">
	<a href="social media"></a>
</div>
</div>

<div class="row">
	<div class="main">
	<?php
	echo "<div class=main_row>";
	if(isset($_GET[go01])) {
		$birthday=$_GET[birthday];
		if (!preg_match("#^[0-9]{2}.[0-9]{2}.[0-9]{4}$#", $birthday)){
			echo "<p class='warning'>please type your birthday in digits as <i>'DD.MM.YYYY'</i></p>";
			die;
		}
	}
	echo "</div>";
	echo "<div class=main_row>";
	if(isset($_GET[go01])) {
		$bday_array=explode(".", $birthday);
		$bday_array[3] = strftime("%B",mktime(0,0,0,$bday_array[1]));
		if ($bday_array[0]=='1' || $bday_array[0]=='21' || $bday_array[0]=='31') { $bday_array[4]=$bday_array[0]."st"; }
		elseif ($bday_array[0]=='2' || $bday_array[0]=='22') { $bday_array[4]=$bday_array[0]."nd"; }
		elseif ($bday_array[0]=='3' || $bday_array[0]=='23') { $bday_array[4]=$bday_array[0]."rd"; }
		else { $bday_array[4]=$bday_array[0]."th"; }
		$bday_array[5]=date('Y', strtotime('+1 year'))-$bday_array[2];
		$bday_array[6]=date('Y')."-".$bday_array[1]."-".$bday_array[0];
		
		
		if ($bday_array[2]<1954) echo "<p>List of number-one songs in Germany on $bday_array[3] $bday_array[4] (starting in $bday_array[2]*)<br><div class='note'>*but unfortunately the german charts started in 1954</div></p>";
		else echo "<p>List of number-one songs in Germany on $bday_array[3] $bday_array[4] (starting in $bday_array[2])</p>";
		
		$sql="SELECT * FROM bh_date INNER JOIN bh_title ON bh_title.id = bh_date.title_id INNER JOIN bh_artist ON bh_title.artist_id = bh_artist.id WHERE date IN ( SELECT MAX(date) FROM bh_date WHERE MONTH(date) <= '$bday_array[1]' AND DAY(date) <= '$bday_array[0]' GROUP BY YEAR(date)) ORDER BY `bh_date`.`date` DESC LIMIT $bday_array[5]";
		$conn = new mysqli($servername, $username, $password, $dbname);
		mysqli_set_charset($conn,'utf8');
		$result2 = $conn->query($sql);
		$conn->close();
		$result=[];
		if($result2->num_rows > 0) { while($row = $result2->fetch_assoc()) array_push($result,$row); }
		
		// IS THERE ALREADY A CURRENT NUMBER ONE FOR THIS YEAR?
		$latest=strtotime($result[0][date]);
		$bday=strtotime($bday_array[6]);
		$timeout=6*24*60*60;
		$secs=$bday-$latest;
		if($secs>$timeout) {
			unset($result[0]);
			$step=array_values($result);
			$result=array_reverse($step);
		}
		else {
			$step=$result;
			$result=array_reverse($step);
		}
		
		for($i=0;$i<count($result);$i++){
			if($result[$i][sp_code]==""){
				$result[$i][image_url]="/media/spotify.jpg"; // width=300
			}
		}
		
		echo "<div class=output>";
		$year = $bday_array[2];
		for($i=0;$i<count($result);$i++){
			echo "<div class=item>";
			echo "<h3>".$year." - ";
			if($i==0)echo "Your birthday!";
			else if($i==1) echo "{$i}st birthday";
			else if($i==2) echo "{$i}nd birthday";
			else if($i==3) echo "{$i}rd birthday";
			else echo "{$i}th birthday";
			echo "</h3>";
			echo "<p><img src='".$result[$i][image_url]."' width=250 class=image_responsive alt='".$result[$i][artist]." - ".$result[$i][title]."'></img></p>";
			echo "<h3>".$result[$i][title]."</h3>";
			echo "<h5>".$result[$i][artist]."</h5>";
			echo "</div>";
			$year++;
		}
		echo "</div>";
	}
	echo "</div>";
	?>
	</div>
</div>
</body>
</html>
