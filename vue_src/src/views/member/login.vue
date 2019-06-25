<template>
<div class="main">
  <el-form class="user-layout-login" :rules= "rules" ref="formLogin" id="formLogin" :model="form" label-width= '' >
    <el-tabs v-model="activeName"  style="margin: 0 auto" tab-position="top" stretch @tab-click="handleClick">
      <el-tab-pane label="账号密码登录" name="tab1">
        <el-form-item label="" prop="name" :rules = "[
              {required: true,message: '请输入邮箱地址', trigger: 'blur'},
              {type:'email', message: '请输入正确的邮箱地址',trigger: ['blur','change']}
            ]">
          <el-input v-model="form.name" type="text"  placeholder="帐户名或邮箱地址 / 123456"></el-input>
        </el-form-item>

        <el-form-item label="" prop='password'  :rules = "[
              {required:true, message: '请输入密码', trigger: 'blur'}
            ]">
          <el-input v-model="form.password" type="password"  placeholder="密码 / 123456"></el-input>
        </el-form-item>
      </el-tab-pane>

      <el-tab-pane label="手机号登录" name="tab2">
        <el-form-item label="" prop="phone" >
          <!-- :rules = "[
              {required:true, message: '请输入手机号', trigger: 'blur'},
              {type:'number', message: '请输入正确的手机号', trigger: ['blur','change']}
            ]" -->
          <el-input v-model="form.phone" type="text"  placeholder="手机号"></el-input>
        </el-form-item>
        <el-row>
          <el-col :span='14'>
            <el-form-item label="" prop='captcha'  :rules = "[
                  {required:true, message: '请输入验证码', trigger: 'blur'},
                  {type:'number', message: '请输入正确的验证码', trigger: ['blur','change']}
                ]">
              <el-input v-model.number="form.captcha"    placeholder="验证码"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span='10'><el-button
            :disabled = 'isSending'
            class="getCaptcha"
            @click.stop.prevent="getCaptcha"
          >{{tips}}</el-button></el-col>
        </el-row>
      </el-tab-pane>
    </el-tabs>
    <!-- 公共form -->
    <el-form-item>
      <el-checkbox label="自动登录" v-model="form.remeberMe" style="float: left"></el-checkbox>
      <a href="#" class="forget-password" style="float: right">忘记密码</a>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="submitForm('formLogin')">登录</el-button>
      <el-button @click="resetForm('formLogin')">重置</el-button>
    </el-form-item>
  </el-form>
</div>
</template>

<script>
import { getCaptcha } from '@/api/user'
// require('../../assets/test.css')
export default {
  data () {
    var checkAge = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('手机号码不能为空.'))
      }
    }
    return {
      isSending: false,
      stepCaptchaVisible: false,
      state: {
        time: 60,
        smsSendBtn: false
      },
      createform: '',
      login: false,
      activeName: 'tab1',
      form: {
        name: '',
        password: '',
        captcha: '',
        phone: '',
        remeberMe: true
      },
      rules: {
        phone: [{
          validator: checkAge, trigger: 'blur'
        }]
      },
      tips: '获取验证码2'
    }
  },
  mounted () {
    /*
    const fun = ( resolve ) => Promise.resolve(
      //    组件定义对象
      console.log('resolve')
    //throw new Error('手动返回错误')
    resolve('成功了')
    )
    fun()
    */
  },
  methods: {
    // event of get captcha
    getCaptcha (e) {
      // this.log(e)
      var that = this
      // 手机号码
      var flag = 1
      this.$refs['formLogin'].validateField(['phone'], (valid) => {
        // that.log(valid) // 输出提示 true
        if (valid) {
          that.log('captcha success')
          flag = 0
        } else {
          that.log('captcha fail')
          return false
        }
      })
      if (flag === 1) {
        // 通过
        that.log(flag)
        // that.$message.loading('验证码发送中……', 0)
        // that.log(this)
        var times = 6
        const interval = window.setInterval(() => {
          if (times-- > 1) {
            // console.log(times)
            that.isSending = true
            that.tips = '验证码发送中' + times + 's'
          } else {
            that.isSending = false
            that.tips = '重新获取验证码'
            window.clearInterval(interval)
          }
        }, 1000)
      } else {
        // 手机号验证未通过 promise 类型
        getCaptcha('17666069836').then(
          function (response) {
            console.log(response)
          })
        that.log('flag:' + flag)
      }
    },
    // checkInstall () {
    //   checkInstall().then(res => {
    //     if (!checkResponse(res)) {
    //       this.$router.push({name: 'install'})
    //       return false
    //     }
    //     info().then(res => {
    //       this.$store.dispatch('setSystem', res.data)
    //     })
    //   })
    // },

    handleClick (tab, event) {
      console.log(tab, event)
    },
    submitForm (formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          console.log('submit success')
        } else {
          console.log('error submit')
          return false
        }
      })
    },
    resetForm (formName) {
      this.$refs[formName].resetFields()
    },
    // judge value of the loginType
    handleUsernameOrEmail (rule, value, callback) {
      const regex = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/
      if (regex.test(value)) {
        this.loginType = 0
      } else {
        this.loginType = 1
      }
    }
  }
}
</script>
