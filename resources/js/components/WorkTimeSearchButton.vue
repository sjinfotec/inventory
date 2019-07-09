<template>
  <button class="btn btn-success"  v-on:click="searchclickBtn()">集計開始</button>
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
