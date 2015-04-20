function eddCompareURL(id) {
    var url = "edd-compare-url";
    document.getElementById(url).innerText = document.getElementById(url).innerText + id + ',';
    document.getElementById('edd-compare-button-' + id).style.display = 'none';
    document.getElementById('edd-compare-go-button-' + id).style.display = 'block';
    var elements = document.getElementsByClassName("edd-compare-go");
    for (var i = 0; i < elements.length; i++) {
        elements[i].setAttribute("href", document.getElementById(url).innerText);
    }
}