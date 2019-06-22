import types from './type'
const mutations = {
  /*
    箭头函数== increment(state) { state.count++ }

    increment: state => state.count++,
    decrement: state => state.count--
    */
  [types.INCREMENT] (state) {
    state.count++
  },
  [types.changeLogin] (state, value) {
    // 先改变state 仓库，再改变
    state.Authorization = value.Authorization
    localStorage.setItem('Authorization', value.Authorization)
  }
}
export default mutations
