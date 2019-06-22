<template>
  <div class="hello">
    <el-container class="container-narrow">
      <el-row class="masthead">
        <div class="logo-title">
          <h2 class="muted">
            <!-- <img src="static/logo/b_64.png" style="width:50px;height:50px;margin-bottom:-10px;" alt=""> -->
            doc by xiethan
          </h2>
        </div>
        <div class="header-btn-group pull-right">
          <el-button type="text">
            反馈
          </el-button>
          <router-link to="/team/index" >&nbsp;&nbsp;&nbsp;{{$t('team_mamage')}}</router-link>
            <router-link to="/admin/index" v-if="isAdmin">&nbsp;&nbsp;&nbsp;{{$t('background')}}</router-link>&nbsp;&nbsp;&nbsp;
          <el-dropdown>
            <span class="el-dropdown-link">更多<i class="el-icon-arrow-down el-icon--right"></i></span>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item><router-link to="/user/setting">{{$t("personal_setting")}}</router-link></el-dropdown-item>
              <el-dropdown-item :command="logout">{{$t("logout")}}</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </div>
      </el-row>
    </el-container>
    <el-container class="container-narrow">
      <div class="container-thumbnails">
        <ul class="thumbnails" id="item-list" v-if = "itemList">
          <li class="text-center" v-for="(item, index) in itemList" :key="index">
            <router-link class="thumbnail item-thumbnail"  :to="'/' +  (item.item_domain ? item.item_domain:item.item_id )" title="" >
            <span class="item-setting" @click.prevent="click_item_setting(item.item_id)" :title="$t('item_setting')">
                <i class="el-icon-setting"></i>
            </span>
            <!-- <span class="item-top " @click.prevent="click_item_top(item.item_id,item.top)" :title="item.top ? $t('cancel_item_top'):$t('item_top')" >
                    <i :class="item_top_class(item.top)"></i>
            </span> -->
                  <p class="my-item">{{item.item_name}}</p>
            </router-link>
          </li>
           <li class=" text-center">
                <router-link class="thumbnail item-thumbnail"  to="/page/index" title="">
                <!-- "/item/add" -->
                  <p class="my-item">测试页面</p>
                </router-link>
          </li>
          <li class=" text-center">
                <router-link class="thumbnail item-thumbnail"  to="/page/add" title="">
                <!-- "/item/add" -->
                  <p class="my-item">{{$t('new_item')}}<i class="el-icon-plus"></i></p>
                </router-link>
          </li>

        </ul>
      </div>
    </el-container>
  </div>
</template>
<script>
import scriptjs from 'scriptjs'

export default {
  components: {

  },
  data () {
    return {
      currentData: new Date(),
      itemList: {},
      isAdmin: false
    }
  },
  methods: {
    item_top_class (top) {
      if (top) {
        return 'el-icon-arrow-down'
      };
      return 'el-icon-arrow-up'
    },
    get_item_list () {
      var that = this
      var url = this.DocConfig.server + '/api/item/myList'
      var params = new URLSearchParams()
      that.axios.get(url, params)
        .then(function (response) {
          console.log(response)
          if (response.data.error_code === 0) {
            var json = response.data.data
            that.itemList = json
            that.bind_item_even()
          } else {
            that.$alert(response.data.error_message)
          }
        })
    },
    fetchScript: function (url) {
      return new Promise((resolve) => {
        scriptjs(url, () => {
          resolve()
        })
      })
    },
    bind_item_even () {
    }
  },
  mounted () {
    // 获取项目列表
    this.get_item_list()
  }
}
</script>
<style scoped>
.container-narrow{
  max-width: 700px;
  margin: 0 auto;
}
.masthead {
  width: 100%;
  margin-top: 30px;
}
.header-btn-group {
  margin-left: 0px;
  line-height: 45px;
}
.logo-title {
  float: left;
}
.pull-right {
  float: right
}

  .container-narrow{
    margin: 0 auto;
    max-width: 700px;
  }

  .masthead{
    width: 100%;
    margin-top: 30px;
  }

  .header-btn-group{
   margin-top: -38px;
  }

  .logo-title{
    margin-left: 0px;
  }

  .container-thumbnails{
    margin: 0 auto;
    margin-top: 30px;
    max-width: 700px;
  }

  .my-item{
    margin: 40px 5px;
  }

  .thumbnails>li {
      float: left;
      margin-bottom: 20px;
      margin-left: 20px;
    }

  .thumbnails li a{
    color: #777;
    font-weight: bold;
    height: 100px;
    width: 180px;
  }
  .thumbnails li a:hover,
  .thumbnails li a:focus{
    border-color:#f2f5e9;
    -webkit-box-shadow:none;
    box-shadow:none;
    text-decoration: none;
    background-color: #f2f5e9;
  }

  .thumbnail {
    display: block;
    padding: 4px;
    line-height: 20px;
    border: 1px solid #ddd;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    -webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.055);
    -moz-box-shadow: 0 1px 3px rgba(0,0,0,0.055);
    box-shadow: 0 1px 3px rgba(0,0,0,0.055);
    -webkit-transition: all .2s ease-in-out;
    -moz-transition: all .2s ease-in-out;
    -o-transition: all .2s ease-in-out;
    transition: all .2s ease-in-out;
    list-style: none;
  }

  .item-setting{
    float:right;
    margin-right:15px;
    margin-top:5px;
    display: block;
  }

  .item-top{
    float:right;
    margin-right:5px;
    margin-top:5px;
   display: block;
  }

  .thumbnails li a i{
    color: #777;
    font-weight: bold;
    margin-left: 5px;
  }

</style>
