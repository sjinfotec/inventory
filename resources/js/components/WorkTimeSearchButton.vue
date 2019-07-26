<template>
  <div class="btn-group d-flex" v-on:click="searchclickBtn()">
    <button type="button" class="btn btn-primary btn-lg font-size-rg w-100"><img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt="">この条件で表示する</button>
  </div>
</template>
<script>

export default {
  name: "WorkTimeSearchButton",
  // マウント時
  mounted() {
    console.log("WorkTimeSearchButton Component mounted.");
  },
  methods: {
    searchclickBtn : function() {
      console.log("searchclickBtn start ");
      this.$emit('searchclick-event',event);
    }

  }
};
</script>
