var $ = require('jquery')

$(document).ready(function () {





    /**
     * Delete product from basket
     */
    var button_delete_item = document.querySelectorAll('#btn_sup_product_f_basket');

    for (var a = 0; a < button_delete_item.length; a++) {
        button_delete_item[a].addEventListener('click', function () {
            var code_product = $(this).closest("tr").find("th").html()
            $(this).closest("tr").remove();
            $.ajax({
                url: "/deleteProductFromBasket",
                type: "POST",
                dataType: "json",
                data: {
                    "code_product": code_product,
                },
                async: true,
                success: function (data) {
                    $("#AddToBasket").toggleClass('btn-secondary btn-primary');
                    $('#AddToBasket').removeClass('disabled')
                    var nbrProductOnBasket = parseInt($('.notify').html());
                    nbrProductOnBasket = nbrProductOnBasket - 1;
                    $('.notify').html(nbrProductOnBasket);

                },
                error: function (xhr, textStatus, errorThrown) {
                    alert('Ajax request failed.');
                }
            })
        });
    }
    /**
     * make number of element on basket change after each add
     *  */
    var buttonAddToBasket = document.querySelectorAll('#AddToBasket');
    for (var a = 0; a < buttonAddToBasket.length; a++) {
        buttonAddToBasket[a].addEventListener('click', function () {
            console.log("test")
            var id_product = $('#id_product').html()
            $.ajax({
                url: "/addToBasket",
                type: "POST",
                dataType: "json",
                data: {
                    "id_product": id_product,
                },
                async: true,
                success: function (data) {
                    console.log(data.message)
                    location.reload();
                },
                error: function (xhr, textStatus, errorThrown) {
                    alert('Ajax request failed.');
                }
            })
            var nbrProductOnBasket = parseInt($('.notify').html());
            nbrProductOnBasket = nbrProductOnBasket + 1;
            $('.notify').html(nbrProductOnBasket);
        })
    }



    /*add a new category*/
    $('#btn-add-category').on('click', () => {
        var category_name = $('#category_name').val()
        $.ajax({
            url: "/createCategory",
            type: "POST",
            dataType: "json",
            data: {
                "category_name": category_name,
            },
            async: true,
            success: function (data) {
                console.log(data.message)
                location.reload();
            },
            error: function (xhr, textStatus, errorThrown) {
                alert('Ajax request failed.');
            }
        })

    })
    var options = {
        valueNames: ['name', 'category', 'price', 'code', 'date']
    };
    var hackerList = new List('users', options);

    $("i").on('click', (function () {
        if ($(this).hasClass("fa-sort-numeric-down")) {
            $(this).removeClass("fa-sort-numeric-down").addClass("fa-sort-numeric-up")
        } else if ($(this).hasClass("fa-sort-numeric-up")) {
            $(this).removeClass("fa-sort-numeric-up").addClass("fa-sort-numeric-down")
        };
        if ($(this).hasClass("fa-sort-alpha-down")) {
            $(this).removeClass("fa-sort-alpha-down").addClass("fa-sort-alpha-up")
        } else if ($(this).hasClass("fa-sort-alpha-up")) {
            $(this).removeClass("fa-sort-alpha-up").addClass("fa-sort-alpha-down")
        };

    }));
    $('.state').click(function () {
        //console.log($('#switch:checked').val())
        let name = $(this).prev(".form-check-input").attr('id');
        let input = $(this).prev(".form-check-input");
        console.log(name);

        $.ajax({
            url: "/changeState",
            type: "POST",
            dataType: "json",
            data: {
                "name": name,
            },
            async: true,
            success: function (data) {
                $('#' + name +
                    ':checked').attr('value', '1')
                $("table tbody").find('input[value="1"]').each(function () {
                    $(this).closest("tr").remove();
                    console.log("deleted")
                });
            },
            error: function (xhr, textStatus, errorThrown) {
                alert('Ajax request failed.');
            }
        })

    })

    $('.custom-file-label').hide();
    $('#hide').hide()

    $.uploadPreview({
        input_field: "#image-upload", // Default: .image-upload
        preview_box: "#image-preview", // Default: .image-preview
        label_field: "#image-label", // Default: .image-label
        label_default: "Choisir Une Image", // Default: Choose File
        label_selected: "Changer l'image", // Default: Change File
        no_label: false // Default: false
    });
    setTimeout(function () {
        $("#successMessage").hide()
    }, 5000);
    /*-----------------------------------------------------*/
    var ref = document.querySelectorAll('#code_product')
    var button = document.querySelectorAll('#btn_sup_product');

    for (var a = 0; a < button.length; a++) {
        button[a].addEventListener('click', function () {
            $(this).closest("tr").remove();
            const price = document.querySelectorAll('.price');
            let total_price = 0
            for (var a = 0; a < price.length; a++) {
                total_price += parseFloat(price[a].innerHTML);
            }
            $('.price_total').html(total_price.toFixed(2))
        });
    }

    const price = document.querySelectorAll('.price');
    let total_price = 0
    for (var a = 0; a < price.length; a++) {
        total_price += parseFloat(price[a].innerHTML);
    }
    $('.price_total').html(total_price.toFixed(2))
    let bol_quantity = new Array();

    var select = document.querySelectorAll('#Ultra');
    for (var a = 0; a < select.length; a++) {
        select[a].addEventListener('change', function () {
              console.log(this.validationMessage)
                if(this.value < this.max){
                }else{
                }



            var selectedVal = $(this).val();
            const price = $(this).parent().next().find('.price').html();
            const new_price = price * selectedVal;
            $(this).parent().next().find('.price_t').html(new_price)
            var elements_price = document.querySelectorAll('.price_t');
            var total = 0;
            for (var a = 0; a < elements_price.length; a++) {

                total += parseFloat(elements_price[a].innerHTML);
            }
            $('.price_total').html(total.toFixed(2))

        });
    }
    var now = new Date();
    var dateString = moment(now).format('YYYY-MM-DD');
    $('#date').html(dateString)

/*----------------------------------------------------------------------*/
function checkvalid(valid){
  return valid ===""
}
    $('#btn_order').click(function () {
      var select = document.querySelectorAll('#Ultra');
      console.log(select)
      var array= []

      for (var a = 0; a < select.length; a++) {
        array.push(select[a].validationMessage)
      }
      if(array.every(checkvalid)){
        var quantities = $('.quantity').map(function () {
            return $(this).val();

        });
        var products_el = $('.product_name').map(function () {
            return $(this).html();
        });
        const quantities_values = Object.values(quantities)

        const products_el_values = Object.values(products_el)
        var object_send = products_el_values.map(function (product, i) {
            return {
                product: product,
                quantity: this[i]
            };
        }, quantities_values);
        var selected_f = document.querySelectorAll('.selected_f');
        var Ultra = document.querySelectorAll('#Ultra').forEach(function (el) {
            el.setAttribute('disabled', true);
        });


        for (var a = 0; a < selected_f.length; a++) {

            selected_f[a].innerHTML = quantities_values[a];
        }
        var price_product_total = document.querySelectorAll('.price_product_total');
        var price_table_total = document.querySelectorAll('.price_t');
        var product_final = document.querySelectorAll('.product');
        $(".total_sencond_win").html($('.price_total').html());
        var email_client = $('#email_client').html();
        for (var a = 0; a < product_final.length; a++) {

            $('.bodytable').append(product_final[a]);
        }

        $("#newTable  tr  td[class='td_action']").remove();

        for (var a = 0; a < price_product_total.length; a++) {

            price_product_total[a].innerHTML = price_table_total[a].innerHTML;
        }
        var myJSONText = JSON.stringify(object_send);
        var table = new Object();
        for (var a = 0; a < object_send.length - 2; a++) {

            table[a] = object_send[a];
        }
        var price_total = $('.price_total').html();
        console.log(table);


        $.ajax({
            url: "/orderValided",
            type: "POST",
            dataType: "json",
            data: {
                "order": JSON.stringify(table),
                "email_client": email_client,
                "total_price": price_total
            },
            async: true,
            success: function (data) {
              $('.notvalid').html(" is validated")

                $("#ref").html(data.ref);
                setTimeout(function(){
                    window.location.href = data.redirect;
                  }, 5000);


            },
            error: function (xhr, textStatus, errorThrown) {
                alert('Ajax request failed.');
            }
        })

      }else{
        $('.notvalid').html("Not valid")

        alert('Check limit quantity please')
      }




    });



    function abc() {
        $(".owl-next").click()
    }
    setInterval(abc, 4000);
});
