<?php

  $con = mysql_connect("localhost","freegan6_WebJS","X+cIVCcTL9J!");
  if ($con) {
    mysql_select_db("freegan6_WebJS", $con);

    $name = $_POST["name"];
    $code = $_POST["code"];
    $comment = $_POST["comment"];

    // Verify that an entry exists in the code table.
    $qVerify = "SELECT * FROM `ProgramData` WHERE `Name` = '" . $name . "' LIMIT 1";
    $result = mysql_query($qVerify);
    $assoc = $result ? mysql_fetch_assoc($result) : null;

    if ($assoc && strcmp($assoc["Name"], $name) == 0) {
      // Check for an existing entry in the bookmark table.
      $qSearch = "SELECT * FROM `Bookmarks` WHERE `Name` = '" . $name . "' ORDER BY `Version` DESC LIMIT 1";
      $result = mysql_query($qSearch);
      $assoc = $result ? mysql_fetch_assoc($result) : null;
      if ($assoc) {
        $version = $assoc["Version"] + 1;
      }
      else {
        $version = 1;
      }

        $qInsert = "INSERT INTO `Bookmarks` (`Name`, `Version`, `Comment`, `Code`) VALUES ('$name', '$version', '$comment', '$code');";
        $result = mysql_query($qInsert);

        if ($result) {
          echo("Bookmarked version " . $version);
        }
        else {
          echo("Failed to add bookmark:\n" . $qInsert);
        }
    }
    else {
      echo("Couldn't find " . $name);
    }
  }
?>
