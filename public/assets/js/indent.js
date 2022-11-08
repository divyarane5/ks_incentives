var clickedExpense = '';
var clickedVendor = '';
var BASE_URL = $("#base_url").val();
var ASSET_URL = $("#asset_url").val();
$('document').ready(function () {
    expenseChangeEvent();
    priceQtyChangeEvent();
    RuntimeAddition();
    paymentAmountChangeEvent();

    $("#expense-form").on('submit', function (e) {
        e.preventDefault();
        $(".expense_name_error strong").html('');
        var expense = $("#expense_name").val();
        if (expense == "") {
            $(".expense_name_error strong").html('The name field is required.');
        }
        var form = $(this)[0];
        var data = new FormData(form);
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: data,
            dataType: "JSON",
            cache : false,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status == 1) {
                    var expense = res.expense;
                    var option = '<option value="'+expense.id+'">'+expense.name+'</option>';
                    $("#indent_item_table tr td:first-child .expense_id").each(function (i) {
                        $(this).append(option);
                        $("#expenseModal").modal('hide');
                    });
                    if (clickedExpense != "") {
                        clickedExpense.val(expense.id);
                        clickedExpense.trigger('change');
                    }
                }
            }
        });
    });

    $("#vendor-form").on('submit', function (e) {
        e.preventDefault();
        $(".vendor_name_error strong").html('');
        var expense = $("#vendor_name").val();
        if (expense == "") {
            $(".vendor_name_error strong").html('The name field is required.');
        }
        var form = $(this)[0];
        var data = new FormData(form);
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: data,
            dataType: "JSON",
            cache : false,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status == 1) {
                    var vendor = res.vendor;
                    var option = '<option value="'+vendor.id+'">'+vendor.name+'</option>';
                    if (clickedVendor != "") {
                        clickedVendor.append(option);
                        $("#vendorModal").modal('hide');
                        clickedVendor.val(vendor.id);
                    }
                }
            }
        });
    });

    $("#file-input").on('change', function (e) {
        $("#preview").html("");
        var file = e.target.files,
        imagefiles = $(this)[0].files;
        var i = 0;
        $.each(imagefiles, function(index, value){
            var f = file[i];
            var fileReader = new FileReader();
            fileReader.onload = (function(e) {
                if (f['type'].split('/')[0] == "image") {
                    $("#preview").append('<div class="pip col-sm-3 col-4 boxDiv" align="center" style="margin-bottom: 20px;">' +
                    '<img style="width: 120px; height: 100px;" src="' + e.target.result + '" class="prescriptions">'+
                    '<p style="word-break: break-all;">' + value.name + '</p>'+
                    '<p class="cross-image remove"><i class="tf-icons bx bx-trash"></i></p>'+
                    '<input type="hidden" name="attachmentName[]" value="' + value.name + '">' +
                    '</div>');
                } else {
                    $("#preview").append('<div class="pip col-sm-3 col-4 boxDiv" align="center" style="margin-bottom: 20px;">' +
                    '<img style="width: 120px; height: 100px;" src="'+ASSET_URL+'/assets/img/icons/unicons/file.png" class="prescriptions">'+
                    '<p style="word-break: break-all;">' + value.name + '</p>'+
                    '<p class="cross-image remove"><i class="tf-icons bx bx-trash"></i></p>'+
                    '<input type="hidden" name="attachmentName[]" value="' + value.name + '">' +
                    '</div>');
                }
                $(".remove").click(function(){
                    $(this).parent(".pip").remove();
                });
            });
            fileReader.readAsDataURL(f);
            i++;

        });
    });
    $(".remove").click(function(){
        alert("bb");
        $(this).parent(".pip").remove();
    });
});

function expenseChangeEvent()
{
    $(".expense_id").bind('change', function () {
        var expense_id = $(this).val();
        var tr = getParentElement($(this), 'tr');
        var vendorElement = tr.find("td .vendor_id");
        $.ajax({
            type: 'GET',
            url: BASE_URL+"/vendor_dropdown/"+expense_id,
            dataType: "html",
            success: function (res) {
                vendorElement.html(res);
            }
        });
    });
}

function priceQtyChangeEvent()
{
    $(".quantity, .unit_price").bind('change', function () {
        //line item total
        var tr = getParentElement($(this), 'tr');
        var quantity = tr.find("td .quantity").val();
        var unitPrice = tr.find("td .unit_price").val();
        quantity = (quantity == "") ? 0 : quantity;
        unitPrice = (unitPrice == "") ? 0 : unitPrice;
        var total = quantity*unitPrice;
        tr.find("td.total").html(total);

        calculateIndentFinalTotal();
    });
}

function calculateIndentFinalTotal()
{
    var finalTotal = 0;
    $("#indent_item_table tr td:nth-child(5)").each(function (i) {
        var subTotal = $(this).html();
        if (subTotal != "" && !isNaN(subTotal)) {
            finalTotal += Number(subTotal);
        }
    })
    $(".final-total").html(finalTotal);
}

function RuntimeAddition()
{
    $(".expense_btn").bind('click', function () {
        $("#expense-form")[0].reset();
        $(".expense_name_error strong").html('');
        $("#expenseModal").modal('show');
        var tr = getParentElement($(this), 'tr');
        clickedExpense = tr.find('td .expense_id');
    });

    $(".vendor_btn").bind('click', function () {
        var tr = getParentElement($(this), 'tr');
        var expense_id = tr.find('td .expense_id').val();
        if (expense_id == "") {
            $.alert({
                title: 'Alert!',
                content: 'Please select expense first.',
                type: 'red',
                typeAnimated: true,
            });
            return false;
        }
        $("#vendor_expense_id").val(expense_id);
        clickedVendor = tr.find('td .vendor_id');
        $("#vendor-form")[0].reset();
        $(".vendor_name_error strong").html('');
        $("#vendorModal").modal('show');
    });
}

//add remove indent items
function addIndentItem()
{
    var item = $("#indent_item_table tbody tr:first-child").clone();
    item.removeClass('readonly');
    item.find('select').val("");
    item.find('input').val("");
    item.find('.total').html("-");
    item.find('select .vendor_id').html('<option value="">Select Vendor</option>');
    item.appendTo("#indent_item_table tbody");
    $(".expense_id").unbind('change');
    expenseChangeEvent();
    $(".quantity, .unit_price").unbind('change');
    priceQtyChangeEvent();
    $(".vendor_btn, .expense_btn").unbind('click');
    RuntimeAddition();
}
function removeIndentItem(element)
{
    if ($("#indent_item_table tbody tr").length == 1) {
        $.alert({
            title: 'Alert!',
            content: 'Atleast one items should be added in indent.',
            type: 'red',
            typeAnimated: true,
        });
        return false;
    }
    getParentElement($(element), 'tr').remove();
    calculateIndentFinalTotal();
}

//add remove payment
function addPaymentItem()
{
    var table = $("#payment_item").clone();
    item = table.find("tr");
    item.find('select').val("");
    item.find('textarea').html("");
    item.appendTo("#indent_payment_table tbody");
    $("#indent_payment_table .amount").unbind('change');
    paymentAmountChangeEvent();
}
function removePaymentItem(element)
{
    getParentElement($(element), 'tr').remove();
    calculatePaymentFinalTotal();
}

function paymentAmountChangeEvent()
{
    $("#indent_payment_table .amount").bind('change', function () {
        calculatePaymentFinalTotal();
    });
}

function calculatePaymentFinalTotal()
{
    //overall total
    var finalTotal = 0;
    $("#indent_payment_table tr td:nth-child(3)").each(function (i) {
        var subTotal = $(this).find('.amount').val();
        if (subTotal != "" && !isNaN(subTotal)) {
            finalTotal += Number(subTotal);
        }
    })
    $(".payment-final-total").html(finalTotal);
}
