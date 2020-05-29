<?php include 'php/db.php'; ?>

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

<body>
<div class="header">
<p>
  <div class="big">
    <a href='/'>B1RTHDAY H1TS</a>
  </div>
  <br />What was number one on each of your birthdays?
</div>

<div class="navbar">
<div class="navbar_left">
</div>
<div class="navbar_center">
</div>
<div class="navbar_right">
  <a href="social media"></a>
</div>
</div>

<div class="row">
  <div class="main">
  <form method="get" action="index.php?">
    <label for="birthday">YOUR BIRTHDAY</label>
    <?php
    if (isset($_GET['go01'])) {
      $birthday = $_GET['birthday'];
      if (preg_match("#^[0-9]{2}.[0-9]{2}.[0-9]{4}$#", $birthday)) {
        echo "<input type='text' id='birthday' name='birthday' value=\"$birthday\" autofocus>";
      }
      else echo "<input type='text' id='birthday' name='birthday' placeholder='i.e. 21.02.1984' autofocus>";
    }
    else echo "<input type='text' id='birthday' name='birthday' placeholder='21.02.1984' autofocus>";
    ?>
    <input type="submit" name="go01" value="&#xf002;">
  </form>
  <?php
  echo "<div class=main_row>";
  if (isset($_GET['go01'])) {
    $birthday = $_GET['birthday'];
    if (!preg_match("#^[0-9]{2}.[0-9]{2}.[0-9]{4}$#", $birthday)) {
      echo "<p class='warning'>please type your birthday in digits as <i>'DD.MM.YYYY'</i></p>";
      die;
    }
  }
  echo "</div>";
  echo "<div class=main_row>";
  if (isset($_GET['go01'])) {
    // $bday_array[0] = Day / i.e. '18'
    // $bday_array[1] = Month / i.e. '02'
    // $bday_array[2] = Year / i.e. '1986'
    // $bday_array[3] = Month / i.e. 'February'
    // $bday_array[4] = added 'st' or 'nd' or 'rd' or 'th' / i.e. '18th'
    // $bday_array[5] = creating the limit for database call: i.e. 2019 - 1986 = '33'
    // $bday_array[6] = creating this year's birthday // i.e. '2018-02-18'
    // $bday_array[7] = creating birthday // i.e. '1986-02-18'
    // $bday_array[8] = Day - creating single digit // i.e. '1' instead of '01'
    $bday_array = explode(".", $birthday);
    $bday_array[3] = strftime("%B", mktime(0, 0, 0, $bday_array[1]));
    $bday_array[8] = ltrim($bday_array[0], '0');
    if ($bday_array[8] == '1' || $bday_array[8] == '21' || $bday_array[8] == '31') {
      $bday_array[4] = $bday_array[8] . "st";
    }
    elseif ($bday_array[8] == '2' || $bday_array[8] == '22') {
      $bday_array[4] = $bday_array[8] . "nd";
    }
    elseif ($bday_array[8] == '3' || $bday_array[8] == '23') {
      $bday_array[4] = $bday_array[8] . "rd";
    }
    else {
      $bday_array[4] = $bday_array[8] . "th";
    }
    $bday_array[5] = date('Y', strtotime('+1 year')) - $bday_array[2];
    $bday_array[6] = date('Y') . "-" . $bday_array[1] . "-" . $bday_array[0];
    $bday_array[7] = $bday_array[2] . "-" . $bday_array[1] . "-" . $bday_array[0];

    if ($bday_array[2] <= 1954) {
      echo "<p>List of number-one songs in Germany on $bday_array[3] $bday_array[4] (starting in $bday_array[2]*)<br><div class='note'>*Did you know that the German charts started in March 1954?</div></p>";
    }
    else {
      echo "<p>List of number-one songs in Germany on $bday_array[3] $bday_array[4] (starting in $bday_array[2])</p>";
    }

    $j = 0;
    $result = [];

    // WARNING
    if ($bday_array[0] > 31) {
      echo "<p class='note warning'>WARNING: You typed '" . $bday_array[0] . "' as your birthday</p><div class='note warning'>you may want to check your date again</div><p>";
    }
    if ($bday_array[1] > 12) {
      echo "<p class='note warning'>WARNING: You typed '" . $bday_array[1] . "' as your birthmonth.</p><div class='note warning'>you may want to check your date again</div><p>";
    }
    if ($bday_array[2] < 1923) {
      echo "<p class='note warning'>Birthyear: " . $bday_array[2] . "</p><div class='note warning'>you may want to check your date again</div><p>";
    }

    // SETTING THE YEAR STRAIGHT
    if ($bday_array[2] < 1954) {
      $year = 1954;
    }
    else {
      $year = $bday_array[2];
    }
    $year--;

    $sql = "SELECT * FROM bh_date INNER JOIN bh_title ON bh_title.id = bh_date.title_id INNER JOIN bh_artist ON bh_title.artist_id = bh_artist.id WHERE date IN ( SELECT MAX(date) FROM bh_date WHERE MONTH(date) <= '$bday_array[1]' AND DAY(date) <= '$bday_array[0]' GROUP BY YEAR(date)) ORDER BY `bh_date`.`date` DESC LIMIT $bday_array[5]";
    $conn = new mysqli($servername, $username, $password, $dbname);
    mysqli_set_charset($conn, 'utf8');
    for ($i = date('Y'); $i > $year; $i--) {
      $request_date = $i . "-" . $bday_array[1] . "-" . $bday_array[0];
      $sql = "SELECT * FROM bh_date INNER JOIN bh_title ON bh_title.id = bh_date.title_id INNER JOIN bh_artist ON bh_title.artist_id = bh_artist.id WHERE date <= '$request_date' ORDER BY date DESC LIMIT 1;";
      $result2 = $conn->query($sql);
      if ($result2->num_rows > 0) {
        while($row = $result2->fetch_assoc()) {
          array_push($result, $row);
        }
      }
      $result[$j][year] = $i;
      $j++;
    }
    $conn->close();

    // FILLING IN THE AGE
    $j = $bday_array[5];
    $j--;
    for ($i = 0; $i < count($result); $i++) {
      $result[$i]['age'] = $j;
      $j--;
    }

    // IS THERE ALREADY A CURRENT NUMBER ONE FOR THIS YEAR?
    $latest = strtotime($result[0]['date']);
    $bday = strtotime($bday_array[6]);
    $timeout = 6*24*60*60;
    $secs = $bday-$latest;
    if ($secs > $timeout) {
      unset($result[0]);
      $step = array_values($result);
      $result = array_reverse($step);
    }
    else {
      $step = $result;
      $result = array_reverse($step);
    }

    for ($i = 0; $i < count($result); $i++) {
      if ($result[$i]['sp_code'] == "") {
        $result[$i]['image_url'] = "/media/spotify.jpg"; // width=300
      }
    }

    echo "<div class=output>";
    $year = $bday_array[2];
    for ($i = 0; $i < count($result); $i++) {
      echo "<div class=item>";
      echo "<h3>" . $year . " - ";
      if ($i == 0) {
        echo "Your birthday!";
      }
      else if ($i == 1) {
        echo "{$i}st birthday";
      }
      else if ($i == 2) {
        echo "{$i}nd birthday";
      }
      else if ($i == 3) {
        echo "{$i}rd birthday";
      }
      else {
        echo "{$i}th birthday";
      }
      echo "</h3>";
      echo "<p><img src='" . $result[$i]['image_url'] . "' width=250 class=image_responsive alt='" . $result[$i]['artist'] . " - " . $result[$i]['title'] . "'></img></p>";
      echo "<h3>" . $result[$i]['title'] . "</h3>";
      echo "<h5>" . $result[$i]['artist'] . "</h5>";
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
