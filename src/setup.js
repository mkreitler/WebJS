// Namespace object ///////////////////////////////////////////////////////////
EditorInfo = {
  bAskBeforeSaving: false,
  MIN_SAVE_LENGTH:  10,
}

// Helper Functions ///////////////////////////////////////////////////////////
// getElementById
function $id(id) {
  return document.getElementById(id);
}

//
// output information
function Output(msg) {
  var m = $id("messages");
  m.innerHTML = m.innerHTML + msg;
}

// file drag hover
function FileDragHover(e) {
  e.stopPropagation();
  e.preventDefault();
  e.target.className = (e.type == "dragover" ? "hover" : "");
}

// file selection
function FileSelectHandler(e) {
  // cancel event and hover styling
  FileDragHover(e);
  // fetch FileList object
  var files = e.target.files || e.dataTransfer.files;
  // process all File objects
  for (var i = 0, f; f = files[i]; i++) {
    ParseFile(f);
    UploadFile(f);
  }
}

function UploadFile(file) {
// upload image files
  var xhr = new XMLHttpRequest();
  var bIsImage = file.type === "image/jpeg" || file.type === "image/png";
  var bIsAudio = file.type === "audio/mp3" || file.type === "audio/ogg";
  var bIsText = file.type.indexOf("text") >= 0;

  if (xhr.upload && (bIsImage || bIsAudio || bIsText) && file.size <= $id("MAX_FILE_SIZE").value) {
    // create progress bar
    var o = $id("progress");
    var progress = o.appendChild(document.createElement("p"));
    progress.appendChild(document.createTextNode("upload " + file.name));

    // progress bar
    xhr.upload.addEventListener("progress", function(e) {
      var pc = parseInt(100 - (e.loaded / e.total * 100));
      progress.style.backgroundPosition = pc + "% 0";
    }, false);

    // file received/failed
    xhr.onreadystatechange = function(e) {
      if (xhr.readyState == 4) {
        progress.className = (xhr.status == 200 ? "success" : "failure");
      }
    };    

    // start upload
    if (bIsImage) {
      xhr.open("POST", "php/uploadImage.php", true);
    }
    else if (bIsAudio) {
      xhr.open("POST", "php/uploadAudio.php", true);
    }
    else {
      // Must be text.
      xhr.open("POST", "php/uploadText.php", true);
    }

    xhr.setRequestHeader("X_FILENAME", file.name);
    xhr.send(file);
  }
}

function activateSafeSave() {
  EditorInfo.bAskBeforeSaving = true;
};

function ParseFile(file) {
  Output(
    "<p>File information: <strong>" + file.name +
    "</strong> type: <strong>" + file.type +
    "</strong> size: <strong>" + file.size +
    "</strong> bytes</p>"
  );

  // display text
  if (file.type.indexOf("text") == 0) {
    var reader = new FileReader();
    reader.onload = function(e) {
      Output(
        "<p><strong>" + file.name + ":</strong></p><pre>" +
        e.target.result.replace(/</g, "&lt;").replace(/>/g, "&gt;") +
        "</pre>"
      );
    }
//    reader.readAsText(file);
  }

  // display an image
  if (file.type.indexOf("image") == 0) {
    var reader = new FileReader();
    reader.onload = function(e) {
      Output(
        "<p><strong>" + file.name + ":</strong><br />" +
        '<img src="' + e.target.result + '" /></p>'
      );
    }
//    reader.readAsDataURL(file);
  }  

  if (file.type.indexOf("audio") == 0) {
    var reader = new FileReader();
    reader.onload = function(e) {
      Output(
        "<p><strong>" + file.name + ":</strong><br />" +
        '<img src="' + e.target.result + '" /></p>'
      );
    }
//    reader.readAsDataURL(file);
  }  
}

//
// initialize
function Init() {
  var fileselect = $id("fileselect"),
    submitbutton = $id("submitbutton"),
    filedrag = $id("filedrag");

  if (window.File && window.FileList && window.FileReader) {
    if (!filedrag || typeof(filedrag) === 'undefined') {
      setTimeout(Init, 100);
    }
    else {
      submitbutton = $id("submitbutton");

      // file select
      fileselect.addEventListener("change", FileSelectHandler, false);

      // is XHR2 available?
      var xhr = new XMLHttpRequest();
      if (xhr.upload) {
        // file drop
        filedrag.addEventListener("dragover", FileDragHover, false);
        filedrag.addEventListener("dragleave", FileDragHover, false);
        filedrag.addEventListener("drop", FileSelectHandler, false);
        filedrag.style.display = "block";
        // remove submit button
        submitbutton.style.display = "none";
      }

      loadModules();
    }
  }
}

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
};

var conHistory = {cmdIndex: -1, cmdList:[]};
function parseCommand(index) {
  var ret = true;
  var textArea = null;
  var xmlObj = null;

  // Check for enter key.
  if (window.event.keyCode === 13) {
    // Process command and clear console line.
    textArea = document.getElementsByName("textConsole" + index)[0];
    if (textArea) {
      // Update command history.
      conHistory.cmdList.push(textArea.value);
      conHistory.cmdIndex = conHistory.cmdList.length;

      // Send command.      
      xmlObj = getXmlHttpObject();
      if (xmlObj) {
        var http = getXmlHttpObject();
        var url = "http://www.freegamersjournal.com/WebJS/php/runConsoleCommand.php";
        var i;

        http.open("POST", url, true);

        //Send the proper header information along with the request
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        http.onreadystatechange = function() {
          if (http.readyState === 4 && http.status == 200) {
            if (http.status == 200) {
              alert(http.responseText);
            }
            else {
              alert("Command failed.");
            }
          }
        }
      }

      http.send("command=" + textArea.value);  

      // Clear command.
      textArea.value = "";
    }

    ret = false;
  }
  else if (window.event.keyCode === 38) {
    // Up arrow.
    textArea = document.getElementsByName("textConsole" + index)[0];
    if (textArea) {
      conHistory.cmdIndex = Math.max(0, conHistory.cmdIndex - 1);
      textArea.value = conHistory.cmdList[conHistory.cmdIndex];
    }

    ret = false;
  }
  else if (window.event.keyCode === 40) {
    // Down arrow.
    textArea = document.getElementsByName("textConsole" + index)[0];
    if (textArea) {
      conHistory.cmdIndex = Math.min(conHistory.cmdIndex + 1, conHistory.cmdList.length - 1);
      textArea.value = conHistory.cmdList[conHistory.cmdIndex];
    }

    ret = false;
  }

  return ret;
};

// POST syntax
// xmlhttp.open("POST","ajax_test.asp",true);
// xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
// xmlhttp.send("fname=Henry&lname=Ford");
var modules = [];
function loadModules() {
  var http = getXmlHttpObject();
  var url = "http://www.freegamersjournal.com/WebJS/php/loadModules.php";
  var moduleList = document.getElementsByName("fieldsetModules")[0];
  var i;

  http.open("POST", url, true);

  //Send the proper header information along with the request
  http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  http.onreadystatechange = function() {
    if (moduleList && http.readyState === 4 && http.status == 200) {
      var results = http.responseText.split("~");
      var opt;
      var label;
      var br;

      // <input type="checkbox" class="moduleChk" id="checkGraphics" value="classStructure" checked="checked" />classStructure<br />
      // <input type="checkbox" class="moduleChk" id="checkClasses" value="canvasGraphics" checked="checked" />canvasGraphics<br />

      for (i=0; i<results.length; ++i) {
        results[i] = results[i].replace("\n", "");
        results[i] = results[i].replace(" ", "");

        br = document.createElement("br");

        label = document.createElement("label");

        opt = document.createElement("input");
        opt.type = "checkbox";
        opt.class = "moduleChk";
        opt.name = results[i];
        opt.id = results[i];
        opt.value = results[i];

        label.appendChild(document.createTextNode(results[i]));
        label.appendChild(opt);

        moduleList.appendChild(label);
        moduleList.appendChild(br);

        modules.push(opt);
      }
    }
  }

  http.send("");  

  return false;
};

function addModule() {
  alert("Add Module");
};

function removeModule() {
  alert("Remove Module");
};

function loadCode()
{
  var http = getXmlHttpObject();
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
    if (http.readyState === 4 && http.status == 200 && http.responseText) {
      var results = http.responseText.split("~");
      var bLoadError = false;

      if (typeof(results) === 'undefined') {
        bLoadError = true;
      }
      else if (results.length >= 4) {
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

        for (iMod=0; iMod<modules.length; ++iMod) {
          checkBox = modules[iMod];
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
      else {
        bLoadError = true;
      }

      if (bLoadError) {
        alert("Failure during load.");
      }
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

//    var arg = params[key];
//    if (arg.indexOf("\"") >= 0) {
//      var splitList = arg.split("\"");
//      arg = splitList.join("\\\"");
//      params[key] = arg;
//    }

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
  var code = editAreaLoader.getValue("textCode");
  var bAllowSave = true;

  if (EditorInfo.bAskBeforeSaving || code.length < EditorInfo.MIN_SAVE_LENGTH) {
      bAllowSave = confirm('Are you sure you want to save?');
  }

  if (bAllowSave) {
    EditorInfo.bAskBeforeSaving = false;

    var path = "http://www.freegamersjournal.com/WebJS/php/saveScript.php";
    var moduleStr = "";
    var iMod;
    var moduleCount = 0;
    var isModule = document.getElementById("radioModule").checked;
    var width = isModule ? "0" : document.getElementById("textWidth").value;
    var height = isModule ? "0" : document.getElementById("textHeight").value;

    for (iMod=0; iMod<modules.length; ++iMod) {
      if (modules[iMod].checked) {
        if (moduleCount > 0) {
            moduleStr = moduleStr + ",";
        }

        moduleStr = moduleStr + modules[iMod].value;
        moduleCount += 1;
      }
    }

    var params = {
      width:width,
      height:height,
      name:document.getElementById("textName").value,
      type:isModule ? "0" : "1",
      code:code,
      modules:moduleStr
    };
    postToUrl(path, params, "post");
  }

  return false;
}
