const config = require('./config');

const baseUrl = config.HOST;//域名

// 网络请求
const https = function({
  url = '',
  data = {},
  method
} = {}) {
  wx.showLoading({
    title: '加载中...'
  });
  return new Promise(function(resolve, reject) {
    wx.request({
      url: getUrl(url),
      method: method,
      data: data,
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      complete: function(res) {
        wx.hideLoading();
        if (res.statusCode >= 200 && res.statusCode < 300) {
          resolve(res.data)
        } else {
          reject(res)
        }
      }
    });
  });
}
// 请求网址
const getUrl = function(url) {
  if (url.indexOf('://') == -1) {
    url = baseUrl + url;
  }
  return url
}

// get方法
const get = function(url, data = {}) {
  return https({
    url,
    method: 'GET',
    data
  })
}
// post方法
const post = function(url, data = {}) {
  return https({
    url,
    method: 'POST',
    data
  })
}



/**
 * POST请求的文件上传
 * URL：接口
 * postData：参数，json类型
 * doSuccess：成功的回调函数
 * doFail：失败的回调函数
 */
function FileData(tempFile = {}, doSuccess = null, doFail = null) {

  wx.uploadFile({
    url: baseUrl + tempFile.url,
    filePath: tempFile.filePath,
    name: tempFile.name,
    formData: tempFile.requestData,
    success(res) {
      doSuccess(res.data);
    },
    fail: function () {
      if (doFail) {
        doFail();
      }
    },
  })
}





/**
 * 多文件上传
 * data:上传数据
 */
function uploadFiles(tempFile)
{
  var uploads = [];

  for (var i = 0; i < tempFile.path.length;i++)
  {
    uploads[i] = new Promise(function(resolve, reject) {
      wx.uploadFile({
        url: baseUrl + tempFile.url,
        name: tempFile.name,
        filePath: tempFile.path[i],
        formData: tempFile.requestData,
        success(res) {
          resolve(res)
        },
      })
    })
  }

  return Promise.all(uploads);
}

module.exports = {
  baseUrl,
  get,
  post,
  FileData,
  uploadFiles,
}