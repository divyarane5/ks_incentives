function validateTwoDecimal(e) {
    var t = e.value;
    var val = (t.indexOf(".") >= 0) ? (t.substr(0, t.indexOf(".")) + t.substr(t.indexOf("."), 3)) : t;
    val = val.replace(/[^0-9.]/gi, '');
    e.value = !isNaN(val) ? val : '';
}
