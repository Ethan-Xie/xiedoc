<template>
  <div class="hello">
    <el-container>
      <el-card class = "center-card">
        <el-form ref="form" label-width="0px"    size="medium">
          <h2>{{$t("login")}}</h2>
          <el-form-item label="">
            <el-input type="text" auto-complete="off" :placeholder="$t('username_description')" v-model="username">
            </el-input>
          </el-form-item>
          <el-form-item label="">
            <el-input type="password" auto-complete="off" :placeholder="$t('password')" v-model="password">
            </el-input>
          </el-form-item>
          <el-form-item label="" v-if = "show_v_code">
            <el-input type="text" auto-complete="off" :placeholder="$t('verification_code')" v-model="v_code">
            </el-input>
            <img style="border:#409EFF solid 1px" v-bind:src="v_code_img" alt="验证码" class="v_code_img" v-on:click="change_v_code_img">
          </el-form-item>
        <el-form-item>
          <el-button type="primary" style="width: 100%" @click="onSubmit">{{$t('login')}}</el-button>
        </el-form-item>
        <el-form-item>
          <router-link to="/user/register">{{$t("register_new_account")}}</router-link>
        </el-form-item>
        </el-form>
      </el-card>
    </el-container>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'Login',
  components: {

  },
  data () {
    return {
      username: '',
      password: '',
      v_code_img: '',
      v_code: '',
      show_v_code: false,
      is_show_alter: false
    }
  },
  methods: {
    ...mapActions([
      'changeLogin'
    ]),
    change_v_code_img () {
      this.v_code_img = this.DocConfig.server + '/api/common/verify?rand=' + Math.random()
    },
    onSubmit () {
      this.log('登录提交')
      // this.$message.success('登录成功')
      var that = this
      var url = this.DocConfig.server + '/api/user/login'
      var params = new URLSearchParams()
      params.append('username', this.username)
      params.append('password', this.password)
      params.append('v_code', this.v_code)
      this.log('登录提交2')
      that.axios.post(url, params).then(function (response) {
        if (response.data.error_code === 0) {
          that.$message.success('登录成功')
          var token = response.data.data.token
          that.log(token)
          that.changeLogin({Authorization: token})
          // 跳转到首页

          let redirect = decodeURIComponent(that.$route.query.redirect || '/item/index')
          that.$router.replace({
            path: redirect
          })
        } else if (response.data.error_code === 10211) {
          // 打开验证码
          that.log('log,10211')
          that.show_v_code = true
          that.$message.warning({
            showClose: true,
            message: response.data.error_message
          })
        } else {
          that.$message.error({
            showClose: true,
            message: response.data.error_message
          })
        }
      })
    }
  },
  mounted () {
    // this.v_code_img = this.DocConfig.server + '/api/common/verify'
    // console.log(this.v_code_img)
    this.change_v_code_img()
  }
}
</script>
<style scoped>
.v_code_img {
  margin-top : 20px;
}
</style>
