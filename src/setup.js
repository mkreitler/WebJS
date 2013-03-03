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

function loadCode()
{
  var xmlhttp = getXmlHttpObject();

  var http = new XMLHttpRequest();
  var url = "http://www.freegamersjournal.com/WebJS/php/loadScript.php";
  var params = document.getElementById("textName").value;

  http.open("POST", url, true);

  //Send the proper header information along with the request
  http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  http.onreadystatechange = function() {//Call a function when the state changes.
    if (http.readyState === 4 && http.status == 200) {
      var results = http.responseText.split("~");

      document.getElementById("textWidth").value = "" + parseInt(results[0]);
      document.getElementById("textHeight").value = "" + parseInt(results[1]);

      editAreaLoader.setValue("textCode", results[2])
    }
  }

  http.send("args=" + params);  

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
  var params = {
    width:document.getElementById("textWidth").value,
    height:document.getElementById("textHeight").value,
    name:document.getElementById("textName").value,
    code:editAreaLoader.getValue("textCode")
  };
  postToUrl(path, params, "post");

  return false;
}
