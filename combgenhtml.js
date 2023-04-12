//javascript functions

function table2csv() {
    var csv = [];
    var rows = document.querySelectorAll("table tr");
    console.log(rows.toString());
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (var j = 0; j < cols.length; j++) 
            row.push(cols[j].innerText);
        
        csv.push(row.join('\t'));        
    }
	
    return csv.join('\n');
}


function downloadcsv() {
	window.URL.revokeObjectURL(document.getElementById("csv").href) //not any idea if needed...
    var csv = table2csv();
    var csvblob = new Blob([csv], { type: 'application/octet-stream;charset=utf-8' });
	document.getElementById("csv").href = window.URL.createObjectURL(csvblob);
}

//1.1
function openfile(input) {

    if (document.getElementById("ordinary_radio").checked) return;


    if (document.getElementById("prodids_radio").checked || document.getElementById("mapids_radio").checked) {

        //thanks to user4602228 from SoF
        //var input = document.createElement("input");
        input.type = "file";
    
    
        //the complicated thing...
        input.onchange = e => { 
    
        // getting a hold of the file reference
        var file = e.target.files[0]; 
    
        // setting up the reader
        var reader = new FileReader();
        reader.readAsText(file,'UTF-8');
    
        // here we tell the reader what to do when it's done reading...
        reader.onload = readerEvent => {
            var content = readerEvent.target.result; // this is the content!
            //alert(content); //mine
    
            trysend(content);
    
            //because it's asynchronous, do not expect that you do something "after" trysend
    
            } //reader event
    
        } //input.onchange

    } //prodids radio

    //single try...
    if (document.getElementById("singleid_radio").checked) {
        var content = prompt("put your Id") + "\n";
        //alert("debug: " + content);

        trysend(content);
        //alert("bala" + tx);
        alert("the Id you will produce from: \n\n" + content + "\n");
        //return;
    } //singleid


} //openfile

function trysend(content) {


    fetch(
         'maindom3.php?form=ajaxpost', {
              method: 'POST',
              headers: {'Content-type': 'application/x-www-form-urlencoded'},
              body: 'csvdata=' + content
          }
    )
    .then( response => response.text() )
    .then( txt => {//alert(txt); 
                   //document.getElementById('form_gen').submit();
                   if (document.getElementById("prodids_radio").checked || 
                       document.getElementById("mapids_radio").checked) 
                           document.getElementById('form_gen').submit();
                   } );


} //trysend

//try to preview produce...
function sayProduceBuild(radio_input) {
//??
alert("mode producing");
    if (!(document.getElementById("prodids_radio").checked || document.getElementById("singleid_radio").checked)) return;


    document.getElementById("need_exclude_edit").setAttribute("readonly", "");

    fetch(
         'maindom3.php?form=ajaxpostprodinfo', {
              method: 'POST',
              headers: {'Content-type': 'application/x-www-form-urlencoded'},
              body: 'ajaxmsg=' + radio_input.id
          }
    )
    .then( response => response.text() )
    .then( txt => {//alert(txt); 
                       window.location.reload();
                   } );


}

function sayStandartGenerate() { //if you return from somewhere

    document.getElementById('need_exclude_edit').removeAttribute('readonly');
    document.getElementById('generate').type = 'submit';
    //alert(":" + document.getElementById('generate').type)

}

//help window...
function helpOpen(helpurl) {
    //alert(helpurl);
    var width = window.screen.width/2;
    var height = window.screen.height;
    //alert(width + ' | ' + height);
    var helpWindow = window.open(helpurl, "Help", '"' + 'width=' + width + ',height=' + height + ',left=' + width + ',top=0"');
    //??
    helpWindow.resizeTo(width, height);
    helpWindow.moveTo(width, 0);
    helpWindow.focus();
}

function newlinkOpen(newlinkurl) {
    var newlinkWindow = window.open(newlinkurl, "Newlink", "width=1280,height=1024,left=0,top=0");
    helpWindow.resizeTo(1280, 1024);
    helpWindow.moveTo(0, 0);
    helpWindow.focus();
}



//\1.1

function myalert(message) { //for escaping onblur-alert() loop in chrome
    if (notshowa) {return;}
    alert(message);
    console.log(document.activeElement.id);
    setTimeout(function() {notshowa=false;},10);
}



function validateInt(edit) { 
    console.log("validateInt begins");
    n = document.getElementById(edit).value;
    n = Number(n);
    if (!isNaN(n) && typeof n === 'number' && n % 1 === 0) {
        document.getElementById('generate').disabled = false;
        return true;
    }      
    else {
        document.getElementById('generate').disabled = true; 
        myalert("this value (" + document.getElementById(edit).value + ") has to be integer");
        notshowa = true;
        return false;
   }
}




function disableSend(fromelem) {
    var toDisable = ["sendbtn", "shufflechset"];

    if (charsetInputs.includes(fromelem.id)) {
        toDisable.forEach(function(subm) {document.getElementById(subm).disabled = true;});
    }
}

function chbox_sel_checked(checkbox) {
    if (!chboxes_selects.hasOwnProperty(checkbox.id)) return;

    if (document.getElementById(checkbox.id).checked) {
        chboxes_selects[checkbox.id].forEach(function(sel) {document.getElementById(sel).disabled = false;});
    }
    else {
        chboxes_selects[checkbox.id].forEach(function(sel) {document.getElementById(sel).disabled = true;});
   }

}

function checkOrder(sel) {
    for (var sels in chboxes_selects) {
        selects = chboxes_selects[sels];
        for (i = 0; i < 2; i++) {
            if (selects[i] === sel.id && document.getElementById(selects[0]).value > document.getElementById(selects[1]).value) {
                alert("BAD character order: " + document.getElementById(selects[0]).value + "-" + document.getElementById(selects[1]).value + "!");
                sel.childNodes[i?50:0].selected = true; //indices are doubled, cannot find why for the moment - object specifics
                return;
            } //if
        } //for (i
    } //for (sels
} //func




//jquery part

$(document).ready(function() {
    //$("#pdiv_memo").html("Hello, World!");
//}); //first try


$("#pdialog").dialog({
    autoOpen: false,
    modal: true,
    width: 540,
    height: 380,
    buttons: { 
        Ok: {
                text: "Ok",
                id: "dialogOk",
                click: function() {
    
                    if (isnotascii($("#pdiv_memo").val())) {
                            alert("Non ASCII characters not allowed!");
                            return;
                    }
        
                    if (isduplicate($("#pdiv_memo").val())) {
                            alert("You have duplicate characters!");
                            return;
                        }
        
                    if ([0, 1].includes($("#pdiv_memo").val().length)) {
                        alert("0 or 1 characters not allowed!");
                        return;
                    }
        
                    $("#ccharset_memo").text($("#pdiv_memo").val());
                    //dynamic part
                    $('#form_chset').append('<input type="submit" id="nonexistinginput" name="nonexistinginput" value="processchstext" style="display: none;">');
                    $('#nonexistinginput').click();
                    //dynm
        
                    $(this).dialog("close");
                }
        },
        Cancel: {
                text: "Cancel",
                id: "dialogCancel",
                click: function () {
                    $(this).dialog("close");
                }
        }
    }
});


$("#yourchsetbutton").click(function () {
    $("#pdialog").dialog("open");
});


//ok on enter
$("#pdialog").keypress(function (e) {
  if (e.which === 10 || e.which === 13) {
    $('#dialogOk').click();
    return false;
  }
});


//enter for form_gen - generate


$("#form_gen").find("input").keypress(function (e) {

  if (e.which === 10 || e.which === 13) { //10x to strager, SoF
    e.preventDefault();
    console.log("qwe qw " + event.which);
    //alert(event.which);
    document.getElementById("generate").click();
  }

});


}); //$(document).ready

//jq part


function isduplicate(textval) {
    var textar = textval.split('');
    var uniquear = [...new Set(textval)];
    return (textar.length > uniquear.length);
}

//thanks to elclanrs - SOf
function isnotascii(textval) {
    var asciiregx = /^[ -~]+$/;

    if (!asciiregx.test(textval)) {
      // string has non-ascii characters (from space 32 to tilde 126)
      return true;
    }
    return false;
}


//javascript f.



