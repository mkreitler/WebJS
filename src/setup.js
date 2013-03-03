// XMLHttp ////////////////////////////////////////////////////////////////////
function getXmlHttpObject() {
  var xmlhttp;
  if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  }
  else
  {// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }

  return xmlhttp;
}

// POST syntax
// xmlhttp.open("POST","ajax_test.asp",true);
// xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
// xmlhttp.send("fname=Henry&lname=Ford");

function loadCode()
{
  var xmlhttp = getXmlHttpObject();

  var http = new XMLHttpRequest();
  var url = "http://www.freegamersjournal.com/WebJS/php/loadScript.php";
  var progName = document.getElementById("textName").value;
  var progType = document.getElementById("radioModule").checked === "checked" ? "module" : "program";
  var requiredModules = null;
  var allModules = null;
  var iMod = 0;
  var jMod = 0;
  var checkBox = null;

  http.open("POST", url, true);

  //Send the proper header information along with the request
  http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  http.onreadystatechange = function() {//Call a function when the state changes.
    if (http.readyState === 4 && http.status == 200) {
      var results = http.responseText.split("~");
      var isModule = parseInt(results[0]);

      if (isModule) {
        document.getElementById("radioModule").checked = false;
        document.getElementById("radioProgram").checked = true;
      }
      else {
        document.getElementById("radioModule").checked = true;
        document.getElementById("radioProgram").checked = false;
      }

      document.getElementById("textWidth").value = "" + parseInt(results[1]);
      document.getElementById("textHeight").value = "" + parseInt(results[2]);

      // Update the "modules" checkboxes.
      requiredModules = results[3].split(",");
      allModules = document.getElementsByClassName("moduleChk");

      for (iMod=0; iMod<allModules.length; ++iMod) {
        checkBox = allModules[iMod];
        checkBox.checked = false;

        for (jMod=0; jMod<requiredModules.length; ++jMod) {
          if (checkBox.value === requiredModules[jMod]) {
            checkBox.checked = true;
            break;
          }
        }
      }

      // Add the code to the editor.
      // TODO: force editArea to update the client area
      // of the "Code" text box following a load().
      editAreaLoader.setValue("textCode", results[4]);
    }
  }

  http.send("progName=" + progName + "&progType=" + progType);  

  return false;
}

///////////////////////////////////////////////////////////////////////////////

// Credit to Rakesh Pai via StackOverflow
function postToUrl(path, params, method) {
    method = method || "post"; // Set method to post by default, if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);
    form.setAttribute("target", "_blank");

    for(var key in params) {
        if(params.hasOwnProperty(key)) {

//      var arg = params[key];
//           if (arg.indexOf("\"") >= 0) {
//    var splitList = arg.split("\"");
//    arg = splitList.join("\\\"");
//    params[key] = arg;
//            }

            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }

    document.body.appendChild(form);
    form.submit();
}


function saveCode() {
  var path = "http://www.freegamersjournal.com/WebJS/php/saveScript.php";

  var moduleList = document.getElementsByClassName("moduleChk");
  var modules = "";
  var iMod;
  var moduleCount = 0;
  var isModule = document.getElementById("radioModule").checked;
  var width = isModule ? "0" : document.getElementById("textWidth").value;
  var height = isModule ? "0" : document.getElementById("textHeight").value;

  for (iMod=0; iMod<moduleList.length; ++iMod) {
    // Modules can't include other modules.
    if (isModule) {
      moduleList[iMod].checked = false;
    }

    if (moduleList[iMod].checked) {
      if (moduleCount > 0) {
          modules = modules + ",";
      }

      modules = modules + moduleList[iMod].value;
      moduleCount += 1;
    }
  }

  var params = {
    width:width,
    height:height,
    name:document.getElementById("textName").value,
    type:isModule ? "0" : "1",
    code:editAreaLoader.getValue("textCode"),
    modules:modules
  };
  postToUrl(path, params, "post");

  return false;
}
