import Vue from 'vue'
import Router from 'vue-router'
import HelloWorld from '@/components/HelloWorld'
import Index from '@/components/Index'
import Register from '@/components/user/Register'
import Login from '@/components/user/Login'
import itemIndex from '@/components/item/index'
import itemAdd from '@/components/item/add'
import pageIndex from '@/components/page/index'
import itemShow from '@/components/item/show/index'
Vue.use(Router)
const Test = {
  template: `<div class="user">
  <h2>User 测试环境 </h2>
  <router-view></router-view>
</div>`
}
const router = new Router({
  routes: [
    {
      name: 'member',
      path: '/member',
      component: resolve => require(['@/components/layout/UserLayout'], resolve),
      meta: {mode: 'Login'},
      children: [
        {
          path: 'login',
          name: 'login',
          component: () => import('@/views/member/login'),
          meta: {model: 'Login'}
        },
        {
          path: 'register',
          name: 'register',
          component: () => import('@/views/member/login'),
          meta: {model: 'Login'}
        }
      ]
    },
    {
      path: '/helloworld',
      name: 'HelloWorld',
      component: HelloWorld
    },
    {
      path: '/',
      name: 'Index',
      component: Index,
      children: [
        {
          path: 'test',
          component: Test
        }
      ]
    },
    {
      name: '404',
      path: '/404',
      component: () => import('@/views/error/404')
    },
    {
      name: '403',
      path: '/403',
      component: resolve => require(['@/views/error/403'], resolve),
      meta: {model: 'error'}
    },
    {
      name: '500',
      path: '/500',
      component: resolve => require(['@/views/error/500'], resolve),
      meta: {model: 'error'}
    },
    {
      path: '/user/register',
      name: 'userRegister',
      component: Register
    },
    {
      path: '/user/login',
      name: 'userLogin',
      component: Login
    },
    {
      path: '/item/index',
      name: 'itemIndex',
      component: itemIndex
    },
    {
      path: '/page/index',
      name: 'pageIndex',
      component: pageIndex
    },
    {
      path: '/item/add',
      name: 'itemAdd',
      component: itemAdd
    },
    {
      // 文章个性域名
      path: '/:item_id',
      name: 'itemShow',
      component: itemShow
    }
  ]
})

// 配置 router 文件夹下面的 index.js
router.beforeEach((to, from, next) => {
  if (to.path === '/user/login') {
    console.log('[router.js]login')
    next()
  } else {
    let token = localStorage.getItem('Authorization')
    if (token === 'null' || token === '') {
      next('/user/login')
    } else {
      console.log('[router.js]have Authorization')
      next()
    }
  }

  /*
  const route = ['home', 'list'];
  let isLogin = isLogin;  // 是否登录
  // 未登录状态；当路由到route指定页时，跳转至login
  if (route.indexOf(to.name) >= 0) {
    if (!isLogin) {
      this.$router.push({ path:'/login',});
    }
  }
  // 已登录状态；当路由到login时，跳转至home
  if (to.name === 'login') {
    if (isLogin) {
      this.$router.push({ path:'/home',});;
    }
  }
  next();
*/
})

router.afterEach(router => {
  // 预留
  // window.scroll(0, 0)
})
export default router
