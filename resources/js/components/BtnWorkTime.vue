<template>
  <div id="btnworktime">
    <div v-if="btnMode ==='search'" class="btn-group d-flex" v-on:click="searchclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100" :disabled="isPush">
        <!-- <img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt="">この条件で表示する</button> -->
        この条件で表示する</button>
    </div>
    <div v-if="btnMode === 'basicswitch'" class="btn-group d-flex" v-on:click="switchclickBtn()">
      <button type="button" class="btn btn-outline-primary btn-lg font-size-rg w-100" :disabled="isPush">
        <!-- <img class="icon-size-sm mr-2 pb-1" src="/images/round-outlined-flag-b.svg" alt="">詳細を表示する</button> -->
        詳細を表示する</button>
    </div>
    <div v-if="btnMode === 'detailswitch'" class="btn-group d-flex" v-on:click="switchclickBtn()">
      <button type="button" class="btn btn-outline-primary btn-lg font-size-rg w-100" :disabled="isPush">
        <!-- <img class="icon-size-sm mr-2 pb-1" src="/images/round-outlined-flag-b.svg" alt="">詳細を非表示にする</button> -->
        詳細を非表示にする</button>
    </div>
    <div v-if="btnMode ==='listdemand'" class="btn-group d-flex" v-on:click="listdemandclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100" :disabled="isPush">
        <!-- <img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt="">申請一覧を表示する（直近10件）</button> -->
        申請一覧を表示する（直近10件）</button>
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
        <!-- <img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt=""> -->
        指定年月締め一括集計</button>
    </div>
    <div v-if="btnMode === 'init'" class="btn-group d-flex" v-on:click="initclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        <!-- <img class="icon-size-sm mr-2 pb-1" src="/images/round-restore-b.svg" alt="">初期設定をする</button> -->
        初期設定をする</button>
    </div>
    <div v-if="btnMode === 'store'" class="btn-group d-flex" v-on:click="storeclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">この内容で登録する</button>
    </div>
    <div v-if="btnMode === 'makedemand'" class="btn-group d-flex" v-on:click="makedemandclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        新規作成する</button>
    </div>
    <div v-if="btnMode === 'condstore'" class="btn-group d-flex" v-on:click="condstoreclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        この条件で登録する</button>
    </div>
    <div v-if="btnMode === 'editcopy'" class="btn-group d-flex" v-on:click="editcopyclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        選択した行を複写して作成する</button>
    </div>
    <div v-if="btnMode === 'fix'" class="btn-group d-flex" v-on:click="fixclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        この内容で更新する</button>
    </div>
    <div v-if="btnMode === 'editfix'" class="btn-group d-flex" v-on:click="editfixclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        この内容で編集確定する</button>
    </div>
    <div v-if="btnMode === 'editdemand'" class="btn-group d-flex" v-on:click="editdemandclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        <!-- <img class="icon-size-sm mr-2 pb-1" src="/images/round-restore-w.svg" alt="">申請を編集する</button> -->
        申請を編集する</button>
    </div>
    <div v-if="btnMode === 'checkdemand'" class="btn-group d-flex" v-on:click="checkdemandclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        申請を確認する</button>
    </div>
    <div v-if="btnMode === 'dodemand'" class="btn-group d-flex" v-on:click="dodemandclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        この内容で申請する</button>
    </div>
    <div v-if="btnMode === 'doapproval'" class="btn-group d-flex" v-on:click="doapprovalclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        この申請を承認する</button>
    </div>

    <div v-if="btnMode === 'discharge'" class="btn-group d-flex" v-on:click="dischargeclickBtn()">
      <button type="button" class="btn btn-warning btn-lg font-size-rg w-100" :disabled="isPush">
        申請を取り下げる</button>
    </div>
    <div v-if="btnMode === 'sendback'" class="btn-group d-flex" v-on:click="sendbackclickBtn()">
      <button type="button" class="btn btn-warning btn-lg font-size-rg w-100" :disabled="isPush">
        申請を差し戻し</button>
    </div>
    <div v-if="btnMode === 'back'" class="btn-group d-flex" v-on:click="backclickBtn()">
      <button type="button" class="btn btn-outline-secondary btn-lg font-size-rg w-100" :disabled="isPush">
        戻る</button>
    </div>
    <div v-if="btnMode === 'cancel'" class="btn-group d-flex" v-on:click="cancelclickBtn()">
      <button type="button" class="btn btn-outline-secondary btn-lg font-size-rg w-100" :disabled="isPush">
        キャンセル</button>
    </div>
    <div v-if="btnMode === 'csvcalc'" class="btn-group d-flex" v-on:click="csvcalcclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
      <img class="icon-size-sm mr-2 pb-1" src="/images/round-get-app-w.svg" alt="">
      集計結果をCSVファイルに出力する</button>
    </div>
    <div v-if="btnMode === 'csvsalary'" class="btn-group d-flex" v-on:click="csvsalaryclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
      <img class="icon-size-sm mr-2 pb-1" src="/images/round-get-app-w.svg" alt="">
      給与計算用CSVファイルを出力する</button>
    </div>
  </button>
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
    makedemandclickBtn : function() {
      this.$emit('makedemandclick-event',event);
    },
    condstoreclickBtn : function() {
      this.$emit('condstoreclick-event',event);
    },
    editdemandclickBtn : function() {
      this.$emit('editdemandclick-event',event);
    },
    checkdemandclickBtn : function() {
      this.$emit('checkdemandclick-event',event);
    },
    editcopyclickBtn : function() {
      this.$emit('editcopyclick-event',event);
    },
    dodemandclickBtn : function() {
      this.$emit('dodemandclick-event',event);
    },
    doapprovalclickBtn : function() {
      this.$emit('doapprovalclick-event',event);
    },
    fixclickBtn : function() {
      this.$emit('fixclick-event',event);
    },
    editfixclickBtn : function() {
      this.$emit('editfixclick-event',event);
    },
    dischargeclickBtn : function() {
      this.$emit('dischargeclick-event',event);
    },
    sendbackclickBtn : function() {
      this.$emit('sendbackclick-event',event);
    },
    backclickBtn : function() {
      this.$emit('backclick-event',event);
    },
    cancelclickBtn : function() {
      this.$emit('cancelclick-event',event);
    },
    listdemandclickBtn : function() {
      this.$emit('listdemandclick-event',event);
    },
    csvcalcclickBtn : function() {
      this.$emit('csvcalc-event',event);
    },
    csvsalaryclickBtn : function() {
      this.$emit('csvsalary-event',event);
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
