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
        document.getElementById(event.srcElement.form.id).submit();
    }
}

function changedComboBox(event) {
    if (regexFilt.test(event.srcElement.form.id) || regexIns.test(event.srcElement.form.id)) { //se e' una form di ricerca o di inserimento
        return;
    }

    document.getElementById(event.srcElement.form.id).submit();
}
