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
      <button type="button" 
        class="btn btn-success btn-lg font-size-rg w-100"
        :disabled="isPush"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="edtString"
        @mouseover="edttooltips('update')"
      >
        <img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt="">
        全日分日次集計</button>
    </div>
    <div v-if="btnMode === 'init'" class="btn-group d-flex" v-on:click="initclickBtn()">
      <button type="button" class="btn btn-outline-secondary btn-lg font-size-rg w-100" :disabled="isPush"><img class="icon-size-sm mr-2 pb-1" src="/images/round-restore-b.svg" alt="">初期設定をする</button>
    </div>
    <div v-if="btnMode === 'store'" class="btn-group d-flex" v-on:click="storeclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100">この内容で登録する</button>
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
  data: function() {
    return {
      edtString: ""
    };
  },
  methods: {
    searchclickBtn : function() {
      this.$emit('searchclick-event',event);
    },
    switchclickBtn : function() {
      this.$emit('switchclick-event',event);
    },
    updateclickBtn : function() {
      this.$emit('updateclick-event',event);
    },
    initclickBtn : function() {
      this.$emit('initclick-event',event);
    },
    storeclickBtn : function() {
      this.$emit('storeclick-event',event);
    },
    // tooltips
    edttooltips: function(value1) {
      if (value1 === 'update') {
        this.edtString = "指定年月の全日分の日次集計を一括で行います（数分程度かかります）。終了後は「この条件で表示する」クリックで表示してください。";
      }
    }
  }
};
</script>
