$(document).ready(() => {
//  datetimepicker by https://www.jqueryscript.net/time-clock/Clean-jQuery-Date-Time-Picker-Plugin-datetimepicker.html
  $('#addStartTime').datetimepicker();
  $('#addItemForm')[0].addEventListener('submit', submitForm);


  function submitForm(e) {
    e.preventDefault();
    let form = new FormData($('#addItemForm')[0]);

    form.set('startTime', Math.floor($('#addStartTime').datetimepicker('getValue').getTime() / 1000).toString());
    form.set('endTime', ((Number(form.get('endTime')) * 86400) + Number(form.get('startTime'))).toString());

    let formDataValid = true;
    for (let val of form.values()) {
      if (val === '' || val == null) {
        formDataValid = false;
      }
    }
    if (formDataValid) {
      $('#emptyFieldError').hide();
      $('#addItemError').hide();
      $('#notLoggedIn').hide();
      $.ajax({
        url: '../forms/add_item_form.php',
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form,
        type: 'post',
        success: (data, str, obj) => {
          if (data.status === '1') {
            window.location.href = 'item.php?itemID=' + data.itemID;
          } else {
            $('#addItemError').show();
            $('#addItemError').text(data.errorMessage);
            window.scrollTo(0, 0);
          }
        }
      });
    } else {
      $('#emptyFieldError').show();
      window.scrollTo(0, 0);
    }
  };
});
