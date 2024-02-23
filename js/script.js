var oldVal;

var regexFilt = /Filter$/;
var regexIns = /Ins$/;

function selected(event) {
    oldVal = document.getElementById(event.srcElement.form.id).elements[event.target.name].value;
}

function deselected(event) {
    newVal = document.getElementById(event.srcElement.form.id).elements[event.target.name].value;
    if (newVal != oldVal) {
        console.log("valore cambiato");

        var tmpField = document.createElement("input");
        tmpField.type = "hidden";
        tmpField.name = "update";
        tmpField.value = "generated with js"

        document.getElementById(event.srcElement.form.id).appendChild(tmpField);
        document.getElementById(event.srcElement.form.id).submit();
    }
}

function changedComboBox(event) {
    if (regexFilt.test(event.srcElement.form.id) || regexIns.test(event.srcElement.form.id)) { //se e' una form di ricerca o di inserimento
        return;
    }

    var tmpField = document.createElement("input");
    tmpField.type = "hidden";
    tmpField.name = "update";
    tmpField.value = "generated with js"

    document.getElementById(event.srcElement.form.id).appendChild(tmpField);
    document.getElementById(event.srcElement.form.id).submit();
}

function onDelete(event) {
    if(!(confirm("Sei sicuro di voler eliminare il record"))) {
        event.preventDefault();
    }
}
