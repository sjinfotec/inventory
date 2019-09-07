<template>
  <div id="btnworktime">
    <div v-if="btnMode ==='search'" class="btn-group d-flex" v-on:click="searchclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100" :disabled="isPush"><img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt="">この条件で表示する</button>
    </div>
    <div v-if="btnMode === 'update'" class="btn-group d-flex" v-on:click="updateclickBtn()">
      <button type="button" class="btn btn-danger btn-lg font-size-rg w-100" :disabled="isPush"><img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt="">日次集計を指定月日数分行います（数分程度）。終了後は「この条件で表示する」を押下してください</button>
    </div>
  </div>
</template>
<script>

export default {
  name: "btnworktime",
  props: {
      btnMode: {
          default: "search",
          type: String
      },
      isPush: {
          default: false,
          type: Boolean
      }
  },
  // マウント時
  mounted() {
    console.log("WorkTimeSearchButton Component mounted.");
  },
  methods: {
    searchclickBtn : function() {
      console.log("searchclickBtn start ");
      this.$emit('searchclick-event',event);
    },
    updateclickBtn : function() {
      console.log("updateclickBtn start ");
      this.$emit('updateclick-event',event);
    }

  }
};
</script>
