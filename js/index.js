$(function () {
  var wrapperH = $('.wrapper').height();
  var $code = $('.code');
  var uid = 1;
  var android = false;
  var ua = navigator.userAgent.toLowerCase();
  if (/(android)/i.test(ua)) {
    android = true;
  }

  $code.css({top: (wrapperH - 84 - 30) + 'px'});
  getUid();

  function connectWebViewJavascriptBridge (callback) {
    if (window.WebViewJavascriptBridge) {
      callback(WebViewJavascriptBridge)
    } else {
      document.addEventListener('WebViewJavascriptBridgeReady', function() {
        callback(WebViewJavascriptBridge)
      }, false)
    }
  }
  function getUid () {
    if (android) {
      var result = window.web && web.getUserInfo();
      alert(result)
      uid = (JSON.parse(result)).uid;
      checkCode();
    } else {
      connectWebViewJavascriptBridge(function (bridge) {
        bridge.callHandler('getUserInfo', {}, function(response) {
          alert(response);
          uid = (JSON.parse(response)).uid;
          alert(uid);
          checkCode();
        })
      });
    }
  }
  function checkCode () {
    $.ajax({
      method:'get',
      url:'/backend/check_exist.php',
      data: {uid: uid}
    }).done(function (res) {
      res = JSON.parse(res);
      console.log(res.code);
      if (res.code != 200) {
        $('.modal').show();
      }
    });
  }

  $('.btn-submit').click(function () {
    var code = $('.modal-input').val();

    if (code != '') {
      $.ajax({
        method:'get',
        url:'/backend/submit_code.php',
        data: {
          uid: uid,
          code: code
        }
      }).done(function (res) {
        console.log(res);
        if (res.code != 200) {
          $('.form-part').hide();
          $('.tip-part').show().fadeOut(4000, function() {
            $('.modal').hide();
          });
        }
      });
    } else {
      alert('请填写兑换码哦~');
    }
  });
})