<template>
  <div id="app">
    <!-- <img src="./assets/logo.png"> -->
    <router-view/>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
* {
  margin: 0;
  padding: 0;
  font-family: '微软雅黑';
}
html,body {
  height: 100%;
}
.center-card{
  margin: 60px auto;
}
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 0px;
  height: 100%;
}
a {
  display: inline;
  height: 40px;
  font-family: '微软雅黑';
  line-height: 40px;
  font-size: 14px;
  overflow-x: hidden;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  font-weight: 400px;
  text-decoration: none;
  color: rgb(64, 158, 255)
}
</style>
