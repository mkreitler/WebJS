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
  function writePage($width, $height, $wantsCanvas, $script) {
    global $moduleList;

    $module = strtok($modules, ",");

    echo("<!doctype html>\n");
    echo("<html>\n");
    echo("  <body>\n");

    if ($wantsCanvas) {
      echo("    <canvas id=theCanvas width=" . $width . " height=" . $height . "></canvas>\n");
    }

    echo("    <pre>\n");
    echo("      <script type=\"text/javascript\">\n");
    echo("  <!-- Script Start ----------------------------------->\n");

    if ($wantsCanvas) {
      echo("      var graphics=document.getElementById('theCanvas').getContext('2d');\n");
    }

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
function buildModuleList($modules) {
  global $moduleList;

  $wantsCanvas = false;

  $module = strtok($modules, ",");
  while ($module) {
    if (strcmp($module, "canvasGraphics") == 0) {
      $wantsCanvas = true;
    }
    else {
      // echo("MODULE: " . $module . "\n");
      $moduleList[count($moduleList)] = $module;
    }

    $module = strtok(",");
  }

  return $wantsCanvas;
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

          $wantsCanvas = buildModuleList($modules);

          // print_r($row);
          // echo("<br>");
          // echo("Width: " . $width . "<br>");
          // echo("Height: " . $height . "<br>");
          // echo("Script: " . $script . "<br>");
          // print_r($moduleList);
          // echo("\n");

          if ($width && $height && $moduleList && $script) {
            writePage($width, $height, $wantsCanvas, $script);
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
