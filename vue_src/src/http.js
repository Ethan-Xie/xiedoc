import axios from 'axios'
// import router from './router/index'
// import Vue from 'vue'
// axios 配置  20s
axios.defaults.timeout = 20000
// axios.defaults.baseURL = Vue.DocConfig.server

// http request 拦截器
/*
axios.interceptors.request.use(
  config => {
    // if (store.state.token) {
    // config.headers.Authorization = `token ${store.state.token}`;
    // }
    return config
  },
  // (x) => x+6 代表 function(x) { renturn x+6 }
  // Promise.reject(reason)方法返回一个带有拒绝原因reason参数的Promise对象
  err => {
    return Promise.reject(err)
  }
)
*/
// http response 拦截器
/*
axios.interceptors.request.use(
  Response => {
    if (Response.config.data && Response.config.data.indexOf('redirect_login=false') > -1) {
      // 不跳转到登录
    } else if (Response.data.error_code === 10102) {
      router.replace({
        path: '/user/login',
        query: {redirect: router.currentRoute.fullPath}
      })
    }
    return Response
  }
)
*/

// 请求拦截器，在请求头中添加token
axios.interceptors.request.use(
  config => {
    if (localStorage.getItem('Authorization')) {
      config.headers.Authorization = localStorage.getItem('Authorization')
      console.log(config.url)
    }
    return config
  },
  err => {
    return Promise.reject(err)
    // .catch(res => { console.log('network') })
  }

)

export default axios
