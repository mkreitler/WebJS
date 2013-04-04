<?php
  $command = $_POST["command"];

  $action = strtolower(strtok($command, " \t"));

  if (strcmp($action, "list") == 0) {
    directoryListing(strtok(" \t"), strtok(" \t"), strtok(" \t"));
  }
  else if (strcmp($action, "help") == 0) {
    showHelp(strtok(" \t"), strtok(" \t"));
  }
  else {
    echo("Unknown command:" . $action);
  }
?>

<?php
function showHelp($codeType, $codeName) {
  $con = mysql_connect("localhost","freegan6_WebJS","X+cIVCcTL9J!");
  if ($con) {
    mysql_select_db("freegan6_WebJS", $con);
    if (strcmp(strtolower($codeType), "module") == 0) {
      $query = "SELECT * FROM `ProgramData` WHERE `ProgramType` = 0 AND `Name` = '" . $codeName . "'";
    }
    else {
      $query = "SELECT * FROM `ProgramData` WHERE `ProgramType` = 1 AND `Name` = '" . $codeName . "'";
    }

    $result = mysql_query($query);

    if ($result) {
      $echoLine = false;
      $row = mysql_fetch_row($result);
      $code = $row[6];
      $line = strtok($code, "\n");

      while ($line) {
        if ($echoLine) {
          $echoLine = strstr(strtolower($line), "[end help]") ? false : true;
        }

        if ($echoLine) {
          echo(str_replace("//", "", $line) . "\n");
        }

        if (!$echoLine) {
          $echoLine = strstr(strtolower($line), "[help]") ? true : false;
        }

        $line = strtok("\n");
      }
    }
    else {
      echo("Database command " . $query . " failed.");
    }
  }
  else {
    echo("Failed to connect to database.");
  }
}
?>

<?php
function databaseListing($codeType, $prep, $wildcard) {
  if (!$prep || strcmp(strtolower($prep), "with")) {
    $wildcard = null;
  }

  $con = mysql_connect("localhost","freegan6_WebJS","X+cIVCcTL9J!");
  if ($con) {
    mysql_select_db("freegan6_WebJS", $con);
    if ($wildcard) {
      $query = "SELECT * FROM `ProgramData` WHERE `Name` LIKE '" . $wildcard . "' AND `ProgramType` = " . $codeType;
    }
    else {
      $query = "SELECT * FROM `ProgramData` WHERE `ProgramType` = " . $codeType;
    }

    $result = mysql_query($query);

    if ($result) {
      $row = mysql_fetch_row($result);
      while ($row) {
        echo($row[1] . "\n");
        $row = mysql_fetch_row($result);
      }
    }
    else {
      echo("Database command " . $query . " failed.");
    }
  }
  else {
    echo("Failed to connect to database.");
  }
}
?>

<?php
function directoryListing($target, $prep, $objArg) {
  if ($target) {
    if (strcmp($target, "modules") == 0) {
      databaseListing(0, $prep, $objArg);
    }
    else if (strcmp($target, "programs") == 0) {
      databaseListing(1, $prep, $objArg);
    }
    else {
      $d = dir($target);
      echo "Path: " . $d->path . "\n";
      while (false != ($entry = $d->read())) {
        if (!$objArg || preg_match($objArg, $entry)) {
          echo $entry."\n";
        }
      }
      $d->close();
    }
  }
}
?>
