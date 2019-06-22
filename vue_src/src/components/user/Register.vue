<template>
  <div class="hello">
    <Header> </Header>

    <el-container>
          <el-card class="center-card"  style="width:400px">
            <el-form  status-icon  label-width="0px" class="demo-ruleForm" @keyup.enter.native="onSubmit">
              <h2>{{$t("register")}}</h2>
              <el-form-item label="" >
                <el-input type="text" auto-complete="off" :placeholder="$t('username_description')" v-model="username"></el-input>
              </el-form-item>

              <el-form-item label="" >
                <el-input type="password" auto-complete="off" v-model="password" :placeholder="$t('password')"></el-input>
              </el-form-item>

              <el-form-item label="" >
                <el-input type="password" auto-complete="off" v-model="confirm_password" :placeholder="$t('password_again')"></el-input>
              </el-form-item>

              <el-form-item label="" >
                <el-input type="text" auto-complete="off" v-model="v_code" :placeholder="$t('verification_code')" ></el-input>
                <img v-bind:src="v_code_img" class="v_code_img"   v-on:click="change_v_code_img" >

              </el-form-item>

               <el-form-item label="" >
                <el-button type="primary" style="width:100%;" @click="onSubmit" >{{$t("register")}}</el-button>
              </el-form-item>

              <el-form-item label="" >
                  <router-link to="/user/login">{{$t("login")}}</router-link>
                  &nbsp;&nbsp;&nbsp;

              </el-form-item>
            </el-form>
          </el-card>
    </el-container>

    <Footer> </Footer>

  </div>
</template>
<script>
export default {
  name: 'Register',
  data () {
    return {
      username: '',
      password: '',
      confirm_password: '',
      v_code: '',
      // v_code_img_base: this.DocConfig.server + '/api/common/verify',
      // 这样写是没用的  v_code_img: this.v_code_img_base
      v_code_img: ''
    }
  },
  methods: {
    onSubmit () {
      //  x 登录
      var that = this
      var url = that.DocConfig.server + '/api/user/register'

      var params = new URLSearchParams()
      params.append('username', this.username)
      params.append('password', this.password)
      params.append('confirm_password', this.confirm_password)
      params.append('v_code', this.v_code)

      that.axios.post(url, params)
        .then(function (response) {
          console.log(response)
          if (response.data.error_code === 0) {
            that.$message.success('注册成功')
            console.log(response)
            that.$router.push({path: '/user/login'})
          } else {
            that.change_v_code_img()
            that.$alert(response.data.error_message)
          }
        })
    },
    change_v_code_img () {
      // 验证码
      this.v_code_img = this.DocConfig.server + '/api/common/verify?rand=' + Math.random()
    }
  },
  mounted () {
    this.log(this.DocConfig.server)
    // this.v_code_img = this.v_code_img_base
    this.change_v_code_img()
    // 给body 添加类，设置背景色
    document.getElementsByTagName('body')[0].className = 'gery-bg'
  },
  beforeDestroy () {
    // 去掉背景色
    document.body.removeAttribute('class', 'grey-bg')
  }

}
</script>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

.center-card a {
  font-size: 12px;
}

.v_code_img{
  margin-top: 20px;
}
</style>
