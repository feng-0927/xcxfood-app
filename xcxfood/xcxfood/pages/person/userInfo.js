const app = getApp()

Page({
  data: {
    user: app.globalData.user,
    BASE: app.globalData.BASE,
    CDN: app.globalData.BASE.config.CDN,
    loadingHidden: true,
    sexItems: [
      { name: '男', value: '1', },
      { name: '女', value: '2' }
    ],
  },
  onLoad() 
  {
    this.setData({
      user: wx.getStorageSync("user")
    })
  },
  //切换性别按钮
  radioChange: function (e) {
    this.setData({
      "user.gender": e.detail.value
    });
  },
  //表单提交方法
  userInfo(e)
  {
    var that = this;
    var user = e.detail.value;

    if (user.nickname.trim().length <= 0) {
      that.data.BASE.common.showToast('昵称不能为空');
      return false;
    }

    //手机号码正则验证
    var phoneReg = /^1[34578]\d{9}$/;
    if (!(phoneReg.test(user.mobile.trim()))) {
      that.data.BASE.common.showToast('您填写的手机号码不正确');
      return false;
    }

    //性别
    if (user.gender != 1 && user.gender != 2) {
      that.data.BASE.common.showToast('您选择的性别有误请重新选择');
      return false;
    }

    var requestData = {
      nickname: user.nickname,
      mobile: user.mobile,
      gender: user.gender,
      userid: that.data.user.id
    }

    //显示loading
    that.setData({
      loadingHidden: false
    })


    //发送数据给后台接口
    that.data.BASE.request.post('/user.php?action=updateUser', requestData).then(function (user){
      //隐藏loading
      that.setData({
        loadingHidden: true
      })

      if(user.result)
      {
        wx.setStorageSync('user', user.data);
        that.data.BASE.common.showToast(user.msg, 'success', '/pages/person/person', 'switchTab');
      } else {
        that.data.BASE.common.showToast(user.msg, 'none', '/pages/person/userInfo', 'switchTab');
      }
    });

  },
  updateAvatar(e) {
    //更新头像
    var that = this;

    wx.chooseImage({
      count: 1,
      sizeType: ['compressed'],
      sourceType: ['album', 'camera'], //拍照和相册选择
      success(res) {
        //获取上传图片的url地址
        const tempFilePaths = res.tempFilePaths

        //封装一个上传数据
        var tempFile = {
          name: "avatar",
          url: "/file.php?action=updateAvatar",
          filePath: tempFilePaths[0],
          requestData: {
            userid: that.data.user.id
          }
        }

        that.setData({
          loadingHidden: false
        })

        that.data.BASE.request.FileData(tempFile, function (user) {
          var user = JSON.parse(user);

          //隐藏loading
          that.setData({
            loadingHidden: true
          })

          if (user.result) {
            wx.setStorageSync("user", user.data);
            that.setData({
              user: user.data
            })

            //刷新当前界面
            const pages = getCurrentPages()
            const perpage = pages[pages.length - 1]
            perpage.onLoad()
          }
        })
      }
    })
  },
})