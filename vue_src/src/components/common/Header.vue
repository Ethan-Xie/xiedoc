<template>
<div>
  <!-- <el-button type="warning"  @click="increment">store 加一</el-button><span>实际值：{{count}}token: {{Authorization}}</span> -->
</div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
console.log('hello header')
export default {
  name: 'Header',
  data () {
    return {
      msg: '头部'
    }
  },
  methods: {
    ...mapActions([
      'increment'
    ])
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
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
