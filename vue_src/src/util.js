// 全局函数与变量
export default {
  install (Vue, options) {
    Vue.prototype.log = console.log.bind(console)
    Vue.prototype.DocConfig = {
    // "server":'http://127.0.0.1/showdoc.cc/server/index.php?s=',api/common/verify ?s=
    //  'server': '../server/index.php'
      'server': 'http://doc.com',
      'local_name': 'http://localhost'
    // /server/index.php?s= http://doc.com/api/common/verify
    }
    Vue.prototype.copyRight = 'powers by xiethan'
    Vue.prototype.getData = function () {
      console.log('我是插件的方法')
    }
    Vue.prototype.request = function () {
      // helo
    }
    Vue.prototype.log = console.log.bind(console)

    Vue.prototype.getRootPath = function () {
      // "https://blog.csdn.net/auserbb/article/details/79259328"
      return window.location.protocol + '//' + window.location.host + window.location.pathname
    }
    Vue.prototype.isMobile = function () {
      // 判断是否为手机浏览器，如果不是为null ！！ 放回结果为false
      return !!navigator.userAgent.match(/iPhone|iPad|iPod|Android|android|BlackBerry|IEMobile/i)
    }

    // 获取用户信息函数
    Vue.prototype.get_user_info = function (callback) {
      var that = this
      var url = that.DocConfig.server + '/api/user/info'
      var params = new URLSearchParams()
      params.append('redirect_login', false)
      that.axios.post(url, params)
        .then(function (response) {
          if (callback) {
            callback(response)
          }
        })
    }
  }
}
