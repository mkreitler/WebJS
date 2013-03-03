<?php
  function writePage($width, $height, $script) {
    echo("<!doctype html>\n");
    echo("<html>\n");
    echo("  <body>\n");
    echo("  <canvas id=theCanvas width=" . $width . " height=" . $height . "></canvs>\n");
    echo("  <!-- Script Start ----------------------------------->\n");
    echo("    <pre>\n");
    echo("      <script type=\"text/javascript\">\n");
    echo($script);
    echo("      </script>\n");
    echo("    </pre>\n");
    echo("  <!-- Script End ------------------------------------->\n");
    echo("  </body>\n");
    echo("</html>\n");
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
        $script = $row[5];

        // print_r($row);
        // echo("<br>");
        // echo("Width: " . $width . "<br>");
        // echo("Height: " . $height . "<br>");
        // echo("Script: " . $script . "<br>");

        if ($width && $height && $script) {
          writePage($width, $height, $script);
        }
        else {
          echo("Failed to retrieve " . $name . ".<br>");
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
