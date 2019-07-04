<template>
  <button class="btn btn-success"  v-on:click="searchclickBtn()">集計開始</button>
</template>
<script>

export default {
  name: "WorkTimeSearchButton",
  props: {
    searchparam: {
      type: Object
    }
  },
  // マウント時
  mounted() {
    console.log("WorkTimeSearchButton Component mounted.");
  },
  methods: {
    searchclickBtn : function(value) {
      console.log("searchclickBtn start ");
      this.$emit('searchclick-event');
    }

  }
};
</script>
