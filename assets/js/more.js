$(function() {
    var $nowValueElem = $("#now-value");

    $("#add-val-button").on("click", function() {
        if(!$.isNumeric($nowValueElem.val())) {
            $nowValueElem.val(0);
        } else {
            $nowValueElem.val(Number($nowValueElem.val())+1);
        }
    });
    $("#minus-val-button").on("click", function() {
        if(!$.isNumeric($nowValueElem.val())) {
            $nowValueElem.val(0);
        } else {
            $nowValueElem.val(Number($nowValueElem.val())-1);
        }
    });
});
