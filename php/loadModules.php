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

    $listOut = "";
    $qScript = "SELECT * FROM `ProgramData` WHERE `ProgramType` = 0 ORDER BY `Name`";
//    echo($qScript . "<br>");

    $result = mysql_query($qScript);

    if ($result) {
      $row = mysql_fetch_row($result);
      while ($row) {
        if (strlen($listOut) == 0) {
          $listOut = $row[1];
        }
        else {
          $listOut = $listOut . "~" . $row[1];
        }

        $row = mysql_fetch_row($result);
      }

      echo($listOut);
    }
    else {
      echo("Failed to locate program.<br>");
    }
  }
  else {
    echo("Failed to connect to databse.<br>");
  }
?>
