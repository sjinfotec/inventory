<template>
  <div class="card-header bg-transparent pb-2 border-0">
    <h1 class="float-sm-left font-size-rg mb-0">{{ headerText1 }}</h1>
    <span class="float-sm-right font-size-sm mb-0">{{ headerText2 }}</span>
  </div>
</template>

<script>

export default {
  name: "dailyworkingtimePanelHeader",
  props: {
    headerText1: {
        type: String,
        default: ''
    },
    headerText2: {
        type: String,
        default: ''
    }
  }
};
</script>
