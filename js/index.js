$(function () {
  var uid = 2;
  var android = false;
  var ua = navigator.userAgent.toLowerCase();
  if (/(android)/i.test(ua)) {
    android = true;
  }

  getUid();
  //checkCode();

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
      setTimeout(function () {
        alert(window.web);
        var result = window.web && web.getUserInfo();
        alert(result);
        uid = (JSON.parse(result)).uid;
        alert(uid);
        checkCode();
      }, 1000);
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
      url: 'http://studio.windra.in/ldl-wzmt/backend/check_exist.php',
      data: {uid: uid},
      type: 'GET',
      cache:false,
      success: function (res) {
        res = JSON.parse(res);
        if (res.code == 200 && res.claimed == 'false') {
          $('.modal').show();
        } else if (res.code == 200 && res.claimed == 'true') {
          $('.code').attr('src', res.codeUrl).css({display: 'block'});
        } else {
          alert(res.msg);
        }
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
        },
        cache:false
      }).done(function (res) {
        res = JSON.parse(res);
        if (res.code == 200) {
          $('.form-part').hide();
          $('.code').attr('src', res.codeurl).css({display: 'block'});
          $('.tip-part').show().fadeOut(3000, function() {
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