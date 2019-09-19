var request = require("request.js")
var config = require('config')

//提醒框
const showToast = (title='',icon = 'none',url = null, type = 'navigeteTo',duration=1000) => {
    wx.showToast({
        title: title,
        icon: icon,
        duration: duration
    })

    //跳转
    if(url)
    {
        setTimeout(function(){
            if (type == 'navigateTo')
            {
                //关闭当前页面，返回上一页面或多级页面
                wx.navigateTo({ url: url });
            }else if(type == 'reLaunch')
            {
                //关闭所有页面，打开到应用内的某个页面
                wx.reLaunch({ url: url });
            }else if(type == 'redirectTo')
            {
                //关闭当前页面，跳转到应用内的某个页面
                wx.redirectTo({ url: url });    
            }else if(type == 'switchTab')
            {
                //跳转到tabBar页面（在app.json中注册过的tabBar页面），同时关闭其他非tabBar页面
                wx.switchTab({ url: url });
            }else
            {
                // 关闭所有页面，打开到应用内的某个页面 默认
                wx.reLaunch({ url: url });
            }
        },duration)
    }
}

//获取url参数值的方法封装
const getUrlParam = (url) => {
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
      var pos = url.indexOf("?");
      var str = url.substr(pos+1);
      var strs = str.split("&");
      for (var i = 0; i < strs.length; i++) {
        theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
      }
    }
    return theRequest;
}

//确认对话框
const showModal = (msg='') => {
    return new Promise((resolve, reject) => {
        wx.showModal({
        title: '提示',
        content: msg,
        success(res) {
            if (res.confirm) {
            resolve(true)
            } else if (res.cancel) {
            reject(false)
            }
        }
        })
    });
}

/**
 * 时间戳转化为年 月 日 时 分 秒
 * ts: 传入时间戳
 * format：返回格式，支持自定义，但参数必须与formateArr里保持一致
*/
const format = (timestamp, format='Y-M-D') => {
    const formateArr = ['Y', 'M', 'D', 'h', 'm', 's'];
    let returnArr = [];
  
    let date = new Date(timestamp*1000);
    let year = date.getFullYear()
    let month = date.getMonth() + 1
    let day = date.getDate()
    let hour = date.getHours()
    let minute = date.getMinutes()
    let second = date.getSeconds()
    returnArr.push(year, month, day, hour, minute, second);
  
    returnArr = returnArr.map(formatNumber);
  
    for (var i in returnArr) {
      format = format.replace(formateArr[i], returnArr[i]);
    }
    return format;
  
}

const formatNumber = n => {
    n = n.toString()
    return n[1] ? n : '0' + n
}

//判断是否授权
const isAuth = () => {
    wx.getSetting({
      success(res) {
        if (res.authSetting['scope.userInfo']) {
          // 已经授权，获取用户信息
          getUser();
        } else {
          wx.navigateTo({
            url: '/pages/user/login',
          })
        }
      }
    })
}
  
  //授权后重新获取用户信息
  const getUser = (callback = null) => {
    var that = this;
    wx.login({
      success(login) {
        wx.getUserInfo({
          success(res)
          {
            var userInfo = res.userInfo
            var data = {
              nickname: userInfo.nickName,
              gender: userInfo.gender,
              code: login.code
            };
            request.post('/user.php?action=login', data).then((res) => {
              if (res.result) 
              {
                if (callback != null) {
                  callback(res);
                } else {
                  //设置本地存储
                  wx.setStorageSync("user", res.data);
                }
              }
            });
          }
        })
      }
    })
}

module.exports = {
    showToast,
    showModal,
    getUrlParam,
    format,
    isAuth,
    getUser
}