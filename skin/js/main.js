$( document ).ready(function() {
    doRequest($("#ajax-request-button"));
    $("#ajax-request-button").click(function(event) {
        doRequest(this);
        event.preventDefault();
    });
    
    backToTop();
});

function doRequest(arg) {
    var primeNumberAmount = $('#prime-number-amount').val();
    if (parseInt(primeNumberAmount) <= 0 || isNaN(parseInt(primeNumberAmount))) {
        alert('Please fill valid number');
        return false;
    }
    $('#ajax-request-button span').hide();
    $('#ajax-request-button img').show();
    if ($('#prime-number-list').length > 0) {
        $('#prime-number-list').remove();
    }
    $.ajax({
        url: $(arg).attr("href"),
        type: "get",
        dataType: "json",
        cache: false,
        data: {'limit': primeNumberAmount},
        success: function(data)
        {
            setTimeout(function(){
                updateHtml(data);
            }, 500 );
            
        }
    });
}

function updateHtml(data) {
    var ulElement = $("<ul/>", {
        id : 'prime-number-list',
    }).addClass('prime-number-list list-inline list-unstyled');
    $.each(data, function(index, value) {
        $("<li/>", {
            text: value
        }).appendTo(ulElement);
    });
    ulElement.appendTo('#prime-number-list-wrapper');
    $('#ajax-request-button span').show();
    $('#ajax-request-button img').hide();
}

function backToTop() {
    var scrollTrigger = 100;
    backToTop = function () {
        var scrollTop = $(window).scrollTop();
        if (scrollTop > scrollTrigger) {
            $('#back-to-top').addClass('show');
        } else {
            $('#back-to-top').removeClass('show');
        }
    };
    $(window).on('scroll', function () {
        backToTop();
    });
    $('#back-to-top').on('click', function (e) {
        e.preventDefault();
        $('html,body').animate({
            scrollTop: 0
        }, 700);
    });
    if(window.location.hash) {
        $('#back-to-top').addClass('show');
    }
}
