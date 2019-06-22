// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
// element-ui 与 vue-i18n 相结合。
import enLocale from 'element-ui/lib/locale/lang/en'
import zhLocale from 'element-ui/lib/locale/lang/zh-CN'
import myZhLocale from '../static/lang/zh-CN'
import myEnLocale from '../static/lang/en'
import VueI18n from 'vue-i18n'
import util from '@/util.js'
import axios from '@/http'
import 'url-search-params-polyfill'
import 'babel-polyfill'
import store from './store'

// header footer 组件
import Header from '@/components/common/Header'
import Footer from '@/components/common/Footer'

// 注册全局变量
Vue.use(util)
Vue.use(ElementUI)
Vue.use(VueI18n)
Vue.config.productionTip = false

Vue.prototype.axios = axios

// 公共组件
Vue.component('Header', Header)
Vue.component('Footer', Footer)

// 多语言相关
var allZhLocale = Object.assign(zhLocale, myZhLocale)
var allEnLocale = Object.assign(enLocale, myEnLocale)

// Vue.config.lang = DocConfig.lang
Vue.config.lang = 'zh-cn'
Vue.locale('zh-cn', allZhLocale)
Vue.locale('en', allEnLocale)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  store,
  router,
  components: { App },
  template: '<App/>'
})
