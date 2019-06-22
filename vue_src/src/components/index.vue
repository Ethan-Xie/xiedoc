<template>
  <div class="hello">
    <Header></Header>
    <div class="block">
      <div class="row header  ">
        <div class="right pull-right">
          <ul class="inline pull-right">
            <li style="float:right;list-style:none;"><router-link :to="link">{{link_text}}</router-link></li><router-view></router-view>
            <li  style="float:right;list-style:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
            <li style="float:right;list-style:none;"><router-link :to="test_link">测试</router-link></li>
            <li  style="float:right;list-style:none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
            <li style="float:right;list-style:none;"><router-link to="/user/login">登录链接</router-link></li>
          </ul>
          </div>
        </div>

      <el-carousel :height="height" :autoplay="false" arrow="always">
        <el-carousel-item style="background-color: #007aff;">

          <div class="slide">
              <!-- <img src="static/logo/b_64.png" alt=""> -->
              <h2>{{$t("section_title1")}}</h2>
              <p><span v-html="$t('section_description1')"></span></p>
              <p>
                  <!-- <a class="el-button "  :href="server_demo" target="_blank">{{$t("demo")}}</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <a class="el-button" :href="server_help" target="_blank" >{{$t("help")}}&nbsp;</a> -->
                  <el-button type="warning"  @click="increment">store 加一</el-button><span>实际值：{{count}}token: {{Authorization}}</span>
              </p>
          </div>

        </el-carousel-item>

      </el-carousel>
    </div>

  </div>
</template>

<script>
// import store from '../store'
// mapGetters mapGetters, mapActions mapMutations mapState install Store  createNamespacedHelpers version: "3.1.0"
import { mapState, mapActions } from 'vuex'
export default {
  name: 'Index',
  data () {
    return {
      height: '',
      link: '',
      link_text: '',
      test_link: '/test',
      server_demo: this.DocConfig.local_name + '/demo',
      server_help: this.DocConfig.local_name + '/help'
    }
  },
  methods: {
    /*
    increment () {
      this.$store.commit('increment')
    },
    */
    ...mapActions([
      'increment'
    ]),
    getHeight () {
      var winHeight = ''
      if (window.innerHeight) {
        winHeight = window.innerHeight
      } else if ((document.body) && (document.body.clientHeight)) {
        winHeight = document.body.clientHeight
      }
      this.height = winHeight + 'px'
    }
  },
  computed: {
    /*
    count () {
      return this.$store.state.count
    }
    */
    ...mapState([
      'count',
      'Authorization'
    ])
  },
  mounted () {
    var that = this
    this.getHeight()
    that.link = '/user/register'
    that.link_text = that.$t('index_login_or_register')
    // this.log(x)
    // this.log(this.$message)
    // 检查是否登录
    this.get_user_info(function (response) {
      console.log(response)
      if (response.data.error_code === 0) {
        that.link = '/item/index'
        that.link_text = that.$t('my_item')
      }
    })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

  .el-carousel__item {
    text-align: center;
    font: 25px "Microsoft Yahei";
    color: #fff;
  }

  .header{
   padding-right: 100px;
   padding-top: 30px;
   font-size: 18px;
   position: fixed;
      right: 0;
      left: 0;
      z-index: 1030;
      margin-bottom: 0;
  }
  .header a {
      color: white;
      font-size: 12px;
      font-weight: bold;
  }
  .slide{
    width: 700px;
    position  : absolute;
    top       : 50%;
    left      : 50%;
    transform : translate(-50%,-50%);
    padding-top: 0px;
    padding-left: 15px;
    padding-right: 15px;
    padding-bottom: 0px;
    box-sizing: border-box;
  }

</style>
