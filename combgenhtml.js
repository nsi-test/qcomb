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




