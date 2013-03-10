<?php
  // Global Variables
  $moduleList = array();
  ?>

<?php
function includeModules() {
  global $moduleList;

  $numModules = count($moduleList);
  for ($i=0; $i<$numModules; ++$i) {
    $qScript = "SELECT * FROM `ProgramData` WHERE `Name` = '" . $moduleList[$i] . "' AND `ProgramType` = 0 LIMIT 1";

    // echo($qScript);

    $result = mysql_query($qScript);
    if ($result) {
      $row = mysql_fetch_row($result);

      echo("\n// MODULE: " . $moduleList[$i] . "//////////////////////////////////////////////\n");
      echo($row[6] . "\n");
      echo("// END MODULE: " . $moduleList[$i] . "//////////////////////////////////////////\n\n");
    }
  }
}
?>

<?php
  function writePage($width, $height, $script) {
    global $moduleList;

    $module = strtok($modules, ",");

    echo("<!doctype html>\n");
    echo("<html>\n");
    echo("  <body>\n");

    echo("    <pre>\n");
    echo("      <script type=\"text/javascript\">\n");
    echo("  <!-- Script Start ----------------------------------->\n");

    echo("var _gameWidth = " . $width . ";\n");
    echo("var _gameHeight = " . $height . ";\n");

    includeModules();

    echo("// GAME START /////////////////////////////////////////////////////////\n");
    echo($script);
    echo("// GAME END ///////////////////////////////////////////////////////////\n");

    echo("\n  <!-- Script End ------------------------------------->\n");

    echo("      </script>\n");
    echo("    </pre>\n");
    echo("  </body>\n");
    echo("</html>\n");
  }
?>

<?php
function buildModuleList($modules, $curModule) {
  global $moduleList;

  $localModuleList = array();

  $module = strtok($modules, ",");
  while ($module) {
    $module = str_replace(" ", "", $module);
    $module = str_replace("\n", "", $module);
    $module = str_replace("\t", "", $module);
    $localModuleList[count($localModuleList)] = $module;

    $module = strtok(",");
  }

  $nLocalModules = count($localModuleList);
//  echo("Num modules: " . $nLocalModules . "<br>");
  for ($j=0; $j<$nLocalModules; ++$j) {
    $module = $localModuleList[$j];

    $moduleIncluded = false;
    $addToList = true;

    $nModules = count($moduleList);
    for ($i=0; $i<$nModules; ++$i) {
      if (strcmp($moduleList[$i], $module) == 0) {
//        echo("Found " . $module . "<br>");
        $moduleIncluded = true;
        $addToList = false;
        break;
      }
    }

//    echo("Current module: " . $module . "   moduleIncluded: " . ($moduleIncluded ? 1 : 0) . "<br>");

    if (!$moduleIncluded && strcmp($module, $curModule)) {
      $qScript = "SELECT * FROM `ProgramData` WHERE `Name` = '" . $module . "' AND `ProgramType` = 0 LIMIT 1";
//      echo($qScript . "<br>");

      $result = mysql_query($qScript);
      if ($result) {
        $row = mysql_fetch_row($result);
        if ($row) {
           $submodules = $row[7];

          if (strlen($submodules)) {
//            echo("SUBMODULES: " . $submodules . "<br>");
            buildModuleList($submodules, $module);
          }
        }
      }
    }
    else {
//      echo("Module included: " . $moduleIncluded . "   Modules equal: " . (strcmp($module, $curModule) == 0  ? 1 : 0) . "<br>");
    }

    if ($addToList) {
//      echo("Adding module: " . $module . "<br>");
      $moduleList[count($moduleList)] = $module;
//      echo($moduleList[count($moduleList)]);
    }
  }
}
?>

<?php
  $con = mysql_connect("localhost","freegan6_WebJS","X+cIVCcTL9J!");
  if ($con) {
//    echo("<br><br><br>");

    mysql_select_db("freegan6_WebJS", $con);

    $name = $_POST["textName"];

    $qScript = "SELECT * FROM `ProgramData` WHERE `Name` = '" . $name . "' LIMIT 1";
//    echo($qScript . "<br>");

    $result = mysql_query($qScript);
    if ($result) {
      $row = mysql_fetch_row($result);
      if ($row) {
        $width = $row[3];
        $height = $row[4];
        $programType = intval($row[5]);

        if ($programType == 1) {
          $script = $row[6];
          $modules = $row[7];

          buildModuleList($modules, $name);

          // print_r($row);
          // echo("<br>");
          // echo("Width: " . $width . "<br>");
          // echo("Height: " . $height . "<br>");
          // echo("Script: " . $script . "<br>");
          // print_r($moduleList);
          // echo("\n");

          if ($width && $height && $script) {
            writePage($width, $height, $script);
          }
          else {
            echo("Failed to retrieve " . $name . ".<br>");
          }
        }
        else {
            echo("Cannot run non-programs.<br>");
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
