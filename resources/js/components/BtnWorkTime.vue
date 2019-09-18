<template>
  <div id="btnworktime">
    <div v-if="btnMode ==='search'" class="btn-group d-flex" v-on:click="searchclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100" :disabled="isPush"><img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt="">この条件で表示する</button>
    </div>
    <div v-if="btnMode === 'basicswitch'" class="btn-group d-flex" v-on:click="switchclickBtn()">
      <button type="button" class="btn btn-outline-primary btn-lg font-size-rg w-100" :disabled="isPush"><img class="icon-size-sm mr-2 pb-1" src="/images/round-outlined-flag-b.svg" alt="">詳細を非表示にする</button>
    </div>
    <div v-if="btnMode === 'detailswitch'" class="btn-group d-flex" v-on:click="switchclickBtn()">
      <button type="button" class="btn btn-outline-primary btn-lg font-size-rg w-100" :disabled="isPush"><img class="icon-size-sm mr-2 pb-1" src="/images/round-outlined-flag-b.svg" alt="">詳細を表示する</button>
    </div>
    <div v-if="btnMode === 'update'" class="btn-group d-flex" v-on:click="updateclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush"><img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt="">
        指定年月の全日次集計を行います（数分程度要）。終了後は「この条件で表示する」で表示します。</button>
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
    switchclickBtn : function() {
      console.log("switchclickBtn start ");
      this.$emit('switchclick-event',event);
    },
    updateclickBtn : function() {
      console.log("updateclickBtn start ");
      this.$emit('updateclick-event',event);
    }

  }
};
</script>
