<?php

  $con = mysql_connect("localhost","freegan6_WebJS","X+cIVCcTL9J!");
  if ($con) {
    echo("<br><br><br>");

    mysql_select_db("freegan6_WebJS", $con);

    $width = $_POST["width"] ? intval($_POST["width"]) : 512;
    $height = $_POST["height"] ? intval($_POST["height"]) : 384;
    $name = $_POST["name"];
    $code = $_POST["code"];

    // Out with the old.
    $qDelete = "DELETE FROM `ProgramData` WHERE `Name` = '$name'";
    $result = mysql_query($qDelete);
    if ($result) {
      echo("Deleted old code entry.<br>");
    }

    echo("Width: " . $width . "<br>");
    echo("Height: " . $height . "<br>");
    echo("Name: " . $name . "<br>");
    echo("Code: " . $code . "<br>");

    $qInsert = "INSERT INTO `ProgramData` (`Width`, `Height`, `Name`, `Code`) VALUES ('$width', '$height', '$name', '$code');";
    echo($qInsert . "<br>");

    $result = mysql_query($qInsert);
    if ($result) {
      echo("Code saved.<br>");
    }
    else {
      echo("Save failed.<br>");
    }
  }
?>
