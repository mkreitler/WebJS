<?php
include 'writePage.php';
?>

<?php
  $command = $_POST["command"];

  $action = strtolower(strtok($command, " \t"));
  $what = strtok(" \t");
  $with = strtok(" \t");
  $which = strtok(" \t");
  $tag = strtok(" \t");

  if (strcmp($action, "list") == 0) {
    directoryListing($what, $with, $which, $tag);
  }
  else if (strcmp($action, "recall") == 0) {
    recallFile($what, $with, $which);
  }
  else if (strcmp($action, "help") == 0) {
    showHelp(strtok(" \t"), strtok(" \t"));
  }
  else if (strcmp($action, "?") == 0) {
    showCommands();
  }
  else {
    echo("Unknown command:" . $action . "\n\nTry: 'list', 'help'");
  }
?>

<?php
function recallFile($name, $prep, $version) {
  if (strcmp(strtolower($prep), "version") == 0) {
    $con = mysql_connect("localhost","freegan6_WebJS","X+cIVCcTL9J!");
    if ($con) {
      mysql_select_db("freegan6_WebJS", $con);

      // First, grab the normal load data.
      $qScript = "SELECT * FROM `ProgramData` WHERE `Name` = '" . $name . "' LIMIT 1";

      $result = mysql_query($qScript);
      if ($result) {
        $row = mysql_fetch_row($result);
        if ($row) {
          $width = $row[3];
          $height = $row[4];
          $isModule = $row[5];
          $script = $row[6];
          $modules = $row[7];

          // Next, grab the requested version of the code.
          // TODO: bookmark and retrieve the module information.
          $qScript = "SELECT * FROM `Bookmarks` WHERE `Name` = '" . $name . "' AND `Version` = '" . $version . "' LIMIT 1";

          $result = mysql_query($qScript);
          if ($result) {
            $row = mysql_fetch_assoc($result);
            $script = $row[$Code];

            writePage($isModule, $width, $height, $modules, $script);
          }
          else {
            echo("Failed to retrieve bookmarked version.");
          }
        }
        else {
          echo("Failed to retrieve latest code.");
        }
      }
      else {
        echo("Failed to find code.");
      }
    }
    else {
      echo("Failed to connect to DB.");
    }
  }
  else {
    echo("Syntax is:\n\nrecall <program|module> version <ver_#>");
  }
}
?>

<?php
function showCommands() {
  echo("list <programs | modules> [with <%wildcard%>]\n");
  echo("list <programs | modules> [with tag <myTag>]\n");
  echo("list versions of [<program or module name>]\n");
  echo("list ../images [with <perl_regex>]\n");
  echo("list ../textData [with <perl_regex>]\n");
  echo("list ../audio [with <perl_regex>]\n");
  echo("--------------------------------------------\n");
  echo("recall <program_name> [version <version_#>]\n");
  echo("recall <module_name> [version <version_#>]\n");
  echo("--------------------------------------------\n");
  echo("help module <module_name>\n");
  echo("help program <program_name>\n");
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
function databaseListing($codeType, $prep, $wildcard, $tagFilter) {
  if (!$prep || strcmp(strtolower($prep), "with")) {
    $wildcard = null;
  }

  if (!$wildcard || strcmp(strtolower($wildcard), "tag")) {
    $tagFilter = null;
  }
  else if (strcmp(strtolower($wildcard), "tag") == 0) {
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
        if (!$tagFilter || strstr(strtolower($row[8]), $tagFilter)) {
          echo($row[1] . "\n");
        }
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
function versionListing($prep, $codeName) {
  if (strcmp($prep, "of") == 0) {
    $con = mysql_connect("localhost","freegan6_WebJS","X+cIVCcTL9J!");
    if ($con) {
      mysql_select_db("freegan6_WebJS", $con);

      $qSearch = "SELECT * FROM `Bookmarks` WHERE `Name` = '" . $codeName . "' ORDER BY `Version` DESC";
      $result = mysql_query($qSearch);

      if ($result) {
        $assoc = mysql_fetch_assoc($result);
        while ($assoc) {
          echo("Version" . " " . $assoc["Version"] . ": " . $assoc["Comment"] . "\n");
          $assoc = mysql_fetch_assoc($result);
        }
      }
      else {
        echo("No versions found.");
      }
    }
    else {
      echo("Syntax is:\n\nlist versions of <module or program name>");
    }
  }
  else {
    echo("Failed to connect to database.");
  }
}
?>

<?php
function directoryListing($target, $prep, $objArg, $tagFilter) {
  if ($target) {
    if (strcmp($target, "modules") == 0) {
      databaseListing(0, $prep, $objArg, $tagFilter);
    }
    else if (strcmp($target, "programs") == 0) {
      databaseListing(1, $prep, $objArg, $tagFilter);
    }
    else if (strcmp($target, "versions") == 0) {
      versionListing($prep, $objArg);
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
