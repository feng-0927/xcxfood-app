const app = getApp()

Page({
  data: {
    user: app.globalData.user,
    BASE: app.globalData.BASE,
    CDN: app.globalData.BASE.config.CDN,
    loadingHidden:true
  },
  onLoad()
  {
    this.setData({
      user: wx.getStorageSync("user")
    })
  },
  userInfo(e)
  {
    var that = this;
    var userInfo = e.detail.userInfo;
    if (userInfo)
    {
      wx.login({
        success(login)
        {
          var data = {
            nickname: userInfo.nickName,
            gender: userInfo.gender,
            code: login.code
          };
          that.data.BASE.request.post('/user.php?action=login', data).then((res)=>{
            if(res.result)
            {
              //设置本地存储
              wx.setStorageSync("user",res.data);
              that.setData({
                user:res.data
              });
            }
          });
        }
      })
    }
  },
  updateAvatar(e)
  {
    //更新头像
    var that = this;

    wx.chooseImage({
      count:1,
      sizeType: ['compressed'],
      sourceType: ['album','camera'],//拍照和相册选择
      success(res)
      {
        //获取上传图片的url地址
        const tempFilePaths = res.tempFilePaths

        //封装一个上传数据
        var tempFile = {
          name:"avatar",
          url:"/file.php?action=updateAvatar",
          filePath: tempFilePaths[0],
          requestData:{
            userid:that.data.user.id
          }
        }

        //显示loading加载
        that.setData({
          loadingHidden: false
        })

        that.data.BASE.request.FileData(tempFile,function(user){
          var user = JSON.parse(user);

          //隐藏loading
          that.setData({
            loadingHidden: true
          })

          if(user.result)
          {
            //同步设置本地存储的user数据
            wx.setStorageSync("user",user.data);
            that.setData({
              user:user.data
            })

            const pages = getCurrentPages()
            const perpage = pages[pages.length -1]
            perpage.onLoad()
          }
        })
      }
    })
  },
  clearCache()
  {
    var that = this;

    //清空本地存储
    wx.clearStorage({
      success()
      {
        that.data.BASE.common.getUser(function(res){
          if(res.result)
          {
            //设置本地存储
            wx.setStorageSync("user",res.data);

            that.data.BASE.common.showToast('清除缓存成功!','success');

            //刷新当前界面
            const pages = getCurrentPages()
            const perpage = pages[pages.length -1]
            perpage.onLoad()
          }
        })
      }
    })
  }
})