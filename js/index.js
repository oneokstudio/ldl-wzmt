$(function () {
  var wrapperH = $('.wrapper').height();
  var wrapperW = $('.wrapper').width();
  var $code = $('.code');
  var uid = -1;
  var android = false;
  var ua = navigator.userAgent.toLowerCase();
  if (/(android)/i.test(ua)) {
    android = true;
  }

  setTimeout(function () {
    $code.css({top: (wrapperH - 84 - 10) + 'px'});
    $('.rule').css({top: wrapperW*0.96 + 'px'});
  }, 1000);
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
      uid = (JSON.parse(result)).uid;
      checkCode();
    } else {
      connectWebViewJavascriptBridge(function (bridge) {
        bridge.init();
        bridge.callHandler('getUserId', {}, function(response) {
          uid = (JSON.parse(response)).uid;
          checkCode();
        })
      });
    }
  }
  function checkCode () {
    $.ajax({
      method:'get',
      url:'http://studio.windra.in/ldl-wzmt/backend/check_exist.php',
      data: {uid: uid}
    }).done(function (res) {
      res = JSON.parse(res);
      if (res.code == 200) {
        $('.modal').show();
      }
    });
  }

  $('.btn-submit').click(function () {
    var code = $('.modal-input').val();

    if (code != '') {
      $.ajax({
        method:'post',
        url:'http://studio.windra.in/ldl-wzmt/backend/submit_code.php',
        data: {
          uid: uid,
          code: code
        }
      }).done(function (res) {
        res = JSON.parse(res);
        if (res.code == 200) {
          $('.form-part').hide();
          $('.code').attr('src', res.codeurl).css({display: 'block'});
          $('.tip-part').show().fadeOut(4000, function() {
            $('.modal').hide();
          });
        } else {
          alert(res.msg);
        }
      });
    } else {
      alert('请填写兑换码哦~');
    }
  });
})