<template>
  <div class="hello">
    <div class="bloc">
      <div class="row header">
        <div class="right pull-right">
          <li><span>test in index{{link_text}}</span></li>
          <li>
            <router-link :to="link">1{{link_text}}</router-link>
          </li>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'Index',
  data () {
    return {
      height: '',
      link: 'http://www.baidu.com',
      link_text: 'ini_texst'
    }
  },
  methods: {
    getHeight () {
      var winHeight = ''
      if (window.innerHeight) {
        winHeight = window.innerHeight
      } else if (document.body && document.body.clientHeight) {
        winHeight = document.body.clientHeight
      }
      this.height = winHeight + 'px'
    }
  },
  mounted () {
    var that = this
    this.log('ehll')
    console.log('s:' + that.link_text)
    that.getHeight()
    that.link = '/user/login'
    that.link_text = that.$t('index_login_or_register')
    console.log('s:' + that.link_text)
    /*
    this.get.get_user_info(function (response) {
      if (response.data.error_code === 0) {
        this.link = 'item/index'
        that.link_text = '中文Item'
        // $t('my_item');
      }
    })
    */
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

.header {
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
.slide {
  width: 700px;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding-top: 0px;
  padding-left: 15px;
  padding-right: 15px;
  padding-bottom: 0px;
  box-sizing: border-box;
}
</style>
