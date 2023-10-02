$(window).resize(function() {
    var windowWidth = $(window).width();
    if (windowWidth < 576) {
        $(".container .row > div").removeClass().addClass("col-12");
    } else if (windowWidth < 768) {
        $(".container .row > div").removeClass().addClass("col-sm-6");
    } else if (windowWidth < 992) {
        $(".container .row > div").removeClass().addClass("col-md-4");
    } else {
        $(".container .row > div").removeClass().addClass("col-lg-3");
    }
});
$(document).ready(function() {
    var totalNum = 50;
    $('#total-num').text(totalNum);

    $('#sort').change(function() {
        // 根據所選的排序方式，排序商品
        var sortType = $(this).val();
        console.log('Sort by: ' + sortType);
    });

    $('#min-price, #max-price').keyup(function() {
        // 根據輸入框中的價格，篩選商品
        var minPrice = $('#min-price').val();
        var maxPrice = $('#max-price').val();
        console.log('Price range: ' + minPrice + ' - ' + maxPrice);
    });

    $('#show-num').change(function() {
        // 根據所選的顯示筆數，顯示商品數目
        var showNum = $(this).val();
        $('#total-num').text(showNum);
        console.log('Show ' + showNum + ' products');
    });
});