<template>
  <div class="markdown-editor-box">
    <link rel="stylesheet" href="/static/editor.md/css/editormd.min.css">
    <div :id="editorId" :content="content"></div>
  </div>
</template>
<script>
import scriptjs from 'scriptjs'
// import { defaultConfig } from '../../config/editor.md'
const defaultConfig = {
  width: '100%',
  height: 600,
  path: '/static/editor.md/lib/',
  // theme: 'dark',
  // previewTheme: 'dark',
  // editorTheme: 'pastel-on-dark',
  markdown: '', // 默认填充内容
  lineWrapping: true, // 编辑框不换行
  codeFold: true, // 代码折叠
  placeholder: '请输入...',
  syncScrolling: true,
  saveHTMLToTextarea: true, // 保存 HTML 到 Textarea
  searchReplace: true,
  watch: true, // 实时预览
  htmlDecode: 'style,script,iframe|on*', // 开启 HTML 标签解析，为了安全性，默认不开启
  toolbar: true, // 工具栏
  previewCodeHighlight: true, // 预览 HTML 的代码块高亮，默认开启
  emoji: true,
  taskList: true,
  tocm: true, // Using [TOCM]
  tex: true, // 开启科学公式TeX语言支持，默认关闭
  flowChart: true, // 开启流程图支持，默认关闭
  sequenceDiagram: true // 开启时序/序列图支持，默认关闭,
  // dialogLockScreen: false,      // 设置弹出层对话框不锁屏，全局通用，默认为true
  // dialogShowMask: false,        // 设置弹出层对话框显示透明遮罩层，全局通用，默认为true
  // dialogDraggable: false,       // 设置弹出层对话框不可拖动，全局通用，默认为true
  // dialogMaskOpacity: 0.4,       // 设置透明遮罩层的透明度，全局通用，默认值为0.1
  // dialogMaskBgColor: "#000",    // 设置透明遮罩层的背景颜色，全局通用，默认为#fff
  // imageUpload: false,
  // imageFormats: ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
  // imageUploadURL: "./php/upload.php",
  // onload: function() {
  //    // this.fullscreen();
  //    // this.unwatch();
  //    // this.watch().fullscreen();
  //    // this.setMarkdown("#PHP");
  //    // this.width("100%");
  //    // this.height(480);
  //    // this.resize("100%", 640);
  // },
}

export default {
  props: {
    editorId: {
      'type': String,
      'default': 'markdown-editor'
    },
    content: {
      'type': String,
      'default': ''
    },
    onchange: { // 内容改变时回调，返回（html, markdown, text）
      type: Function
    },
    config: { // 编辑器配置
      type: Object
    },
    initData: {
      'type': String
    },
    initDataDelay: {
      'type': Number, // 延迟初始化数据时间，单位毫秒
      'default': 0
    }
  },
  data: function () {
    return {
      editor: null,
      timer: null,
      doc: {},
      jsLoadOver: false
    }
  },
  methods: {
    fetchScript: function (url) {
      return new Promise((resolve) => {
        scriptjs(url, () => {
          resolve()
        })
      })
    },
    getConfig: function () {
      return { ...defaultConfig, ...this.config }
    },
    getEditor: function () {
      return this.editor
    },
    getDoc: function () {
      return this.doc
    },
    watch: function () {
      return this.editor.watch()
    },
    unwatch: function () {
      return this.editor.unwatch()
    },
    previewing: function () {
      return this.editor.previewing()
    },
    getHTML: function () {
      return this.editor.getHTML()
    },
    getMarkdown: function () {
      return this.editor.getMarkdown()
    },
    setMarkdown: function (markdown) {
      return this.editor.setMarkdown(markdown)
    },
    init (id) {
      let vm = this
      // let editor = vm.$store.state.editor
      // if (editor.goEdit) {
      //   editor.goEdit = false
      // } else {
      //   editor.isEditing = false
      // }
      // this.editor.goEdit = false
      let md = this.content || '### 初始化完成'
      // md = this.content
      console.log(md)
      vm.initEditor(md)
      /*
      vm.axios.get('/interfaceApi/getDocById/' + id)
        .then(function (response) {
          let doc = response.data.pageInfo.list[0]
          vm.doc = doc
          let md = doc.content
          vm.initEditor(md)
        })
        .catch(function (error) {
          vm.$message({
            message: error.data.serverResult.resultMessage,
            type: 'error'
          })
        })
        */
    },
    initEditor: function (markdown) {
      let vm = this
      let config = vm.getConfig()
      if (markdown) {
        config.markdown = markdown
      }
      // 异步加载事件
      (async () => {
        // 异步里的 直线执行
        await this.fetchScript('/static/editor.md/../jquery.min.js')
        await this.fetchScript('/static/editor.md/lib/marked.min.js')
        await this.fetchScript('/static/editor.md/lib/prettify.min.js')
        await this.fetchScript('/static/editor.md/lib/raphael.min.js')
        await this.fetchScript('/static/editor.md/lib/underscore.min.js')
        await this.fetchScript('/static/editor.md/lib/sequence-diagram.min.js')
        await this.fetchScript('/static/editor.md/lib/flowchart.min.js')
        await this.fetchScript('/static/editor.md/lib/jquery.flowchart.min.js')
        await this.fetchScript('/static/editor.md/editormd.min.js')
        vm.jsLoadOver = true
        vm.$nextTick(() => {
          vm.editor = window.editormd(vm.editorId, config)
          vm.editor.on('load', () => {
            setTimeout(() => { // hack bug: 一个页面多个编辑器只能初始化其中一个数据问题
              vm.initData && vm.editor.setMarkdown(vm.initData)
            }, vm.initDataDelay)
          })
          vm.onchange && vm.editor.on('change', () => {
            // console.log(document.querySelector('#markdown-editor').InnerHtml)
            let html = vm.editor.getPreviewedHTML()
            vm.onchange({
              markdown: vm.editor.getMarkdown(),
              html: html,
              text: window.$(html).text()
            })
          })
        })
      })()
    }
  },
  mounted: function () {
    let vm = this
    let docId = vm.$router.currentRoute.name
    vm.init(docId)
    vm.timer = setInterval(function () {
      if (vm.editor && vm.jsLoadOver) {
        try {
          vm.watch()
          vm.previewing()
          vm.previewing()
          window.clearInterval(vm.timer)
          vm.timer = null
        } catch (e) {
        }
      }
    }, 80)
  },
  destroyed: function () {
    let vm = this
    if (vm.timer != null) {
      window.clearInterval(vm.timer)
      vm.timer = null
    }
  }
}
</script>
