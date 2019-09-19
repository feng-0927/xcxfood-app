const config = require('./utils/config');
const request = require('./utils/request');
const common = require('./utils/common');

App({
  globalData: {
    BASE:{
      config,
      request,
      common
    },
    user: null,
  }
})