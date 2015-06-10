/*
* EDD Compare URL
* Function does 3 things:
* 1. Appends download ID to the inner text of a hidden div when button is clicked
* 2. Hides the compare button and shows a go button instead
* 3. Updates the URL for each go button each time another is clicked
*
* @since 0.1
 */
function eddCompareURL(id) {
    var url = "edd-compare-url";
    document.getElementById(url).innerHTML = document.getElementById(url).innerHTML + id + ',';
    document.getElementById('edd-compare-button-' + id).style.display = 'none';
    document.getElementById('edd-compare-go-button-' + id).style.display = 'block';
    var elements = document.getElementsByClassName("edd-compare-go");
    for (var i = 0; i < elements.length; i++) {
        var link = document.getElementById(url).innerHTML.replace(/\&amp\;/g, "&").slice(0, - 1);
        elements[i].setAttribute("href", link);
    }
}