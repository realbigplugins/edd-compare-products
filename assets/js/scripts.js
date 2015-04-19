/**
 * Created by kylemaurer on 4/8/15.
 */
function eddCompareURL(id) {
    var url = "edd-compare-url";
    document.getElementById(url).innerText = document.getElementById(url).innerText + id + ',';
    document.getElementById('edd-compare-button-' + id).style.display = 'none';
    document.getElementById('edd-compare-go-button-' + id).style.display = 'block';
    /* instead of this, I need to get all buttons with edd-compare-go-button- in the ID and update each
    every time this function runs.
     */
    var elements = document.getElementsByClassName("edd-compare-go");
    for(var i=0; i<elements.length; i++) {
        elements[i].setAttribute("href", document.getElementById(url).innerText);
    }
}