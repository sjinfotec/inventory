<template>
  <div class="btn-group d-flex" v-on:click="updateclickBtn()">
    <button type="button" class="btn btn-primary btn-lg font-size-rg w-100"><img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt="">最新状態に更新する（注意！！　時間がかかります）</button>
  </div>
</template>
<script>

export default {
  name: "WorkTimeUpdatreButton",
  // マウント時
  mounted() {
    console.log("WorkTimeUpdatreButton Component mounted.");
  },
  methods: {
    updateclickBtn : function() {
      console.log("updateclickBtn start ");
      this.$emit('updateclick-event',event);
    }

  }
};
</script>
