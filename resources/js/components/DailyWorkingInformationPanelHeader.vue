<template>
  <div class="card-header bg-transparent pb-2 border-0">
    <h1 class="float-sm-left font-size-rg mb-0">{{ this.headertext1 }}</h1>
    <span class="float-sm-right font-size-sm mb-0">{{ this.headertext2 }}</span>
  </div>
</template>

<script>

export default {
  name: "dailyworkingtimePanelHeader",
  props: {
    headertext1: {
        type: String,
        default: ''
    },
    headertext2: {
        type: String,
        default: ''
    }
  }
};
</script>
