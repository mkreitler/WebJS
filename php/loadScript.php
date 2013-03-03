<?php
  function writePage($isModule, $width, $height, $modules, $script) {
    echo($isModule . "~" . $width . "~" . $height . "~" . $modules . "~" . $script);
  }
?>

<?php
  $con = mysql_connect("localhost","freegan6_WebJS","X+cIVCcTL9J!");
  if ($con) {
//    echo("<br><br><br>");

    mysql_select_db("freegan6_WebJS", $con);

    $name = $_POST["progName"];

    $qScript = "SELECT * FROM `ProgramData` WHERE `Name` = '" . $name . "' LIMIT 1";
//    echo($qScript . "<br>");

    $result = mysql_query($qScript);
    if ($result) {
      $row = mysql_fetch_row($result);
      if ($row) {
        $width = $row[3];
        $height = $row[4];
        $isModule = $row[5];
        $script = $row[6];
        $modules = $row[7];

        // print_r($row);
        // echo("<br>");
        // echo("Width: " . $width . "<br>");
        // echo("Height: " . $height . "<br>");
        // echo("Script: " . $script . "<br>");

        if ($width && $height && $script) {
          writePage($isModule, $width, $height, $modules, $script);
        }
        else {
          echo("Failed to retrieve program.<br>");
        }
      }
    }
    else {
      echo("Failed to locate program.<br>");
    }
  }
  else {
    echo("Failed to connect to databse.<br>");
  }
?>
