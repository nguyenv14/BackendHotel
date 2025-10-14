$(window).on('load',function(event){
  $('body').removeClass('preloading');
  $('.load').delay(500).fadeOut('fast'); /* fade fast/400/slow */
});

$(window).on('load', function (event) {
  $('body').removeClass('preloading');
  $('.load').delay(0).fadeOut('fast'); /* fade fast/400/slow */
});



$(document).ready(function () {
  $(window).scroll(function () {
      if ($(this).scrollTop()) {
          $('.navbar').addClass('sticky');
          $('.navbar-item-link').addClass('sticky-color');
      } else {
          $('.navbar').removeClass('sticky');
          $('.navbar-item-link').removeClass('sticky-color');
      }
  });
});



function Validator(options) {

  var selectorRules = {

  };
  // Hàm thực hiện validate
  function validate(inputElement, rule) {

      var errorElement = inputElement.parentElement.querySelector(options.errorSelector);
      var errorMessage;
      // Lấy ra các rules của selector
      var rules = selectorRules[rule.selector];
      // Lặp qua từ rule và kiểm tra
      // có lỗi thì dừng việc kiểm tra
      for (var i = 0; i < rules.length; ++i) {
          errorMessage = rules[i](inputElement.value);
          if (errorMessage) break;
      }

      if (errorMessage) {
          errorElement.innerText = errorMessage;
          inputElement.parentElement.classList.add('invalid');
      } else {
          errorElement.innerText = '';
          inputElement.parentElement.classList.remove('invalid');
      }
  }
  // Lấy element của form cần validate
  var formElement = document.querySelector(options.form);

  if (formElement) {

      options.rules.forEach(function (rule) {
          // Lưu lại các rules cho mỗi input
          if (Array.isArray(selectorRules[rule.selector])) {
              selectorRules[rule.selector].push(rule.test);
          } else {
              selectorRules[rule.selector] = [rule.test];
          }

          var inputElement = formElement.querySelector(rule.selector);



          if (inputElement) {
              // Xử lý trường hợp blur khỏi input
              inputElement.onblur = function () {
                  validate(inputElement, rule);

              }

              // Xử lý trường hợp khi người dùng vào input
              inputElement.oninput = function () {
                  var errorElement = inputElement.parentElement.querySelector(options.errorSelector);
                  errorElement.innerText = '';
                  inputElement.parentElement.classList.remove('invalid');

              }
          }
      });

  }
}

Validator.isRequired = function (selector, message) {
  return {
      selector: selector,
      test: function (value) {
          return value.trim() ? undefined : message || 'Vui lòng nhập trường này'
      }
  };

}

Validator.isEmail = function (selector, message) {
  return {
      selector: selector,
      test: function (value) {
          var regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
          return regex.test(value) ? undefined : message || 'Trường này phải là email'
      }
  };

}

Validator.maxLength = function (selector, max, message) {
  return {
      selector: selector,
      test: function (value) {
          return value.length <= max ? undefined : message || `Chỉ được nhập tối đa ${max} kí tự`
      }
  };

}


Validator.minLength = function (selector, min, message) {
  return {
      selector: selector,
      test: function (value) {
          return value.length >= min ? undefined : message || `Vui lòng nhập tối thiểu ${min} kí tự`
      }
  };

}

Validator.isNumber = function (selector, message) {
  return {
      selector: selector,
      test: function (value) {
          return value === isNaN(value) ? true : message || `Trường này phải là số`
      }
  };

}

Validator.isConfirmed = function (selector, getCofirmValue, message) {
  return {
      selector: selector,
      test: function (value) {
          return value === getCofirmValue() ? undefined : message || 'Giá trị nhập vào không chính xác'
      }
  };
}

$('#overlay').click(function () {

  $("#form-register").css({
      "display": "none"
  });
  $("#from-verycode").css({
      "display": "none"
  });
  $("#overlay").css({
      "display": "none"
  });
  $(".fromlogin").css({
      "display": "none"
  });
  $(".form-vecovery-password").css({
      "display": "none"
  });
  $(".code_confirmation").css({
      "display": "none"
  });
  $(".password_confirmation").css({
      "display": "none"
  });

  $('#ShowImgHotel').hide();

  $('.form-message').html('');

  document.getElementById('name_login').value = '';
  document.getElementById('pass_login').value = '';

  document.getElementById('fullname').value = '';
  document.getElementById('email').value = '';
  document.getElementById('phonenumber').value = '';
  document.getElementById('password').value = '';
  document.getElementById('password_confirmation').value = '';

  document.getElementById('EmailorAccountOld').value = '';

  document.getElementById('very_code_rc').value = '';
  document.getElementById('very_code').value = '';

  document.getElementById('newpass').value = '';
  document.getElementById('newpassconfir').value = '';


});
$('.close-box').click(function () {

  $("#form-register").css({
      "display": "none"
  });
  $(".from-verycode").css({
      "display": "none"
  });
  $("#overlay").css({
      "display": "none"
  });
  $(".fromlogin").css({
      "display": "none"
  });
  $(".form-vecovery-password").css({
      "display": "none"
  });
  $(".code_confirmation").css({
      "display": "none"
  });
  $(".password_confirmation").css({
      "display": "none"
  });

  $('.form-message').html('');

  document.getElementById('name_login').value = '';
  document.getElementById('pass_login').value = '';

  document.getElementById('fullname').value = '';
  document.getElementById('email').value = '';
  document.getElementById('phonenumber').value = '';
  document.getElementById('password').value = '';
  document.getElementById('password_confirmation').value = '';

  document.getElementById('EmailorAccountOld').value = '';

  document.getElementById('very_code_rc').value = '';
  document.getElementById('very_code').value = '';

  document.getElementById('newpass').value = '';
  document.getElementById('newpassconfir').value = '';

});

$('#dangnhap').click(function () {
  $(".fromlogin").css({
      "display": "block"
  });
  $("#overlay").css({
      "display": "block"
  });
});
$('#dangky').click(function () {
  $("#form-register").css({
      "display": "block"
  });
  $("#overlay").css({
      "display": "block"
  });
});
$('#loginaccount').click(function () {
  $("#form-register").css({
      "display": "none"
  });
  $(".fromlogin").css({
      "display": "block"
  });
});
$('#registeraccount').click(function () {
  $("#form-register").css({
      "display": "block"
  });
  $(".fromlogin").css({
      "display": "none"
  });
});
$('#recoverypassaccount').click(function () {
  $(".form-vecovery-password").css({
      "display": "block"
  });
  $(".fromlogin").css({
      "display": "none"
  });
});

Validator({
  form: '.fromlogin',
  errorSelector: '.form-message',
  rules: [
      Validator.isRequired('#email_login', 'Vui lòng nhập email'),
      Validator.isEmail('#email_login'),
      Validator.isRequired('#pass_login', 'Vui lòng nhập mật khẩu của bạn'),
      Validator.minLength('#pass_login', 6),
  ]
});



Validator({
  form: '#form-register',
  errorSelector: '.form-message',
  rules: [
      Validator.isRequired('#fullname', 'Vui lòng nhập tên đầy đủ của bạn'),
      Validator.isRequired('#email', 'Vui lòng nhập email của bạn'),
      Validator.isRequired('#phonenumber', 'Vui lòng nhập số điện thoại của bạn'),
      //Validator.isNumber('#phonenumber', 'Số điện thoại không hợp lệ'),
      Validator.isEmail('#email'),
      Validator.minLength('#password', 6),
      Validator.minLength('#phonenumber', 10),
      Validator.maxLength('#phonenumber', 10),
      Validator.isRequired('#password_confirmation'),
      Validator.isConfirmed('#password_confirmation', function () {
          return document.querySelector('#form-register #password').value;
      }, 'Mật khẩu nhập lại không chính xác')
  ]
});

Validator({
  form: '.form-vecovery-password',
  errorSelector: '.form-message',
  rules: [
      Validator.isRequired('#EmailorAccountOld', 'Vui lòng nhập tên tài khoản hoặc email'),
  ]
});

Validator({
  form: '#form_verycode',
  errorSelector: '.form-message',
  rules: [
      Validator.isRequired('#very_code', 'Hãy nhập vào mã 8 số'),
      Validator.minLength('#very_code', 8),
      //Validator.isNumber('#very_code', 'Trường này phải là số'),
  ]
});

Validator({
  form: '#password_confir',
  errorSelector: '.form-message',
  rules: [
      Validator.minLength('#newpass', 6),
      Validator.isRequired('#newpassconfir'),
      Validator.isConfirmed('#newpassconfir', function () {
          return document.querySelector('#password_confir #newpass').value;
      }, 'Mật khẩu nhập lại không chính xác')

  ]
});

Validator({
  form: '#code_confirmation',
  errorSelector: '.form-message',
  rules: [
      Validator.isRequired('#very_code_rc', 'Hãy nhập vào mã 8 số'),
      Validator.minLength('#very_code_rc', 8),
      //Validator.isNumber('#very_code_rc', 'Trường này phải là số'),
  ]
});
