<template>
  <div id="btnworktime">
    <div v-if="btnMode ==='search'" class="btn-group d-flex" v-on:click="searchclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100" :disabled="isPush">
        <!-- <img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt="">この条件で表示する</button> -->
        この条件で表示する</button>
    </div>
    <div v-if="btnMode === 'basicswitch'" class="btn-group d-flex" v-on:click="switchclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100" :disabled="isPush">
        <!-- <img class="icon-size-sm mr-2 pb-1" src="/images/round-outlined-flag-b.svg" alt="">詳細を表示する</button> -->
        詳細を表示する</button>
    </div>
    <div v-if="btnMode === 'detailswitch'" class="btn-group d-flex" v-on:click="switchclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100" :disabled="isPush">
        <!-- <img class="icon-size-sm mr-2 pb-1" src="/images/round-outlined-flag-b.svg" alt="">詳細を非表示にする</button> -->
        詳細を非表示にする</button>
    </div>
    <div v-if="btnMode ==='listdemand'" class="btn-group d-flex" v-on:click="listdemandclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100" :disabled="isPush">
        <!-- <img class="icon-size-sm mr-2 pb-1" src="/images/round-search-w.svg" alt="">申請一覧を表示する（直近10件）</button> -->
        申請一覧を表示する（直近10件）</button>
    </div>
    <div v-if="btnMode === 'gosubdate' && isDisplay === true" class="btn-group d-flex" v-on:click="gosubdateclickBtn()">
      <button type="button" class="btn btn-outline-primary btn-lg font-size-rg w-100" :disabled="isPush">
        &lt;&lt; 前日</button>
    </div>
    <div v-if="btnMode === 'goadddate' && isDisplay === true" class="btn-group d-flex" v-on:click="goadddateclickBtn()">
      <button type="button" class="btn btn-outline-primary btn-lg font-size-rg w-100" :disabled="isPush">
        翌日 &gt;&gt;</button>
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
        指定年月締め一括集計（指定範囲により数分程度時間要する場合があります）</button>
    </div>
    <div v-if="btnMode === 'init'" class="btn-group d-flex" v-on:click="initclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        この条件でカレンダーを一括設定する</button>
    </div>
    <div v-if="btnMode === 'copyinit'" class="btn-group d-flex" v-on:click="copyinitclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        この条件での前月分を複写設定する</button>
    </div>


    <div v-if="btnMode === 'store'" class="" v-on:click="storeclickBtn()">
      <button type="button" class="" :disabled="isPush">この内容で登録する</button>
    </div>
    
    
    <div v-if="btnMode === 'initstore'" class="btn-group d-flex" v-on:click="initstoreclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">この内容で設定する</button>
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
    <div v-if="btnMode === 'reasonstore'" class="btn-group d-flex" v-on:click="reasonstoreclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        この内容で差異理由を登録する</button>
    </div>
    <div v-if="btnMode === 'back'" class="btn-group d-flex" v-on:click="backclickBtn()">
      <button type="button" class="btn btn-info btn-lg font-size-rg w-100" :disabled="isPush">
        戻る</button>
    </div>
    <div v-if="btnMode === 'ok'" class="btn-group d-flex" v-on:click="okclickBtn()">
      <button type="button" class="btn btn-info btn-lg font-size-rg w-100" :disabled="isPush">
        ＯＫ</button>
    </div>
    <div v-if="btnMode === 'cancel'" class="btn-group d-flex" v-on:click="cancelclickBtn()">
      <button type="button" class="btn btn-info btn-lg font-size-rg w-100" :disabled="isPush">
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
    <div v-if="btnMode === 'csvlog'" class="btn-group d-flex" v-on:click="csvlogclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
      <img class="icon-size-sm mr-2 pb-1" src="/images/round-get-app-w.svg" alt="">
      勤怠ログCSVファイルを出力する</button>
    </div>
    <div v-if="btnMode === 'filedownload'" class="btn-group d-flex" v-on:click="filedownloadclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
      <img class="icon-size-sm mr-2 pb-1" src="/images/round-get-app-w.svg" alt="">
      {{ btnName }}</button>
    </div>
    <div v-if="btnMode === 'csvshift'" class="btn-group d-flex" v-on:click="csvshiftclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
      <img class="icon-size-sm mr-2 pb-1" src="/images/round-get-app-w.svg" alt="">
      ダウンロード</button>
    </div>
    <div v-if="btnMode === 'passreset'" class="btn-group d-flex" v-on:click="passresetclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
        パスワードを変更する</button>
    </div>
    <div v-if="btnMode === 'delete'" class="btn-group d-flex" v-on:click="deleteclickBtn()">
      <button type="button" class="btn btn-danger btn-lg font-size-rg w-100" :disabled="isPush">
        削除する</button>
    </div>
    <div v-if="btnMode === 'usersupload'" class="btn-group d-flex" v-on:click="usersuploadclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
      <img class="icon-size-sm mr-2 pb-1" src="/images/file-upload-icon-vector-01.svg" alt="">
        CSVから作成する</button>
    </div>
    <div v-if="btnMode === 'usersdownload'" class="btn-group d-flex" v-on:click="usersdownloadclickBtn()">
      <button type="button" class="btn btn-success btn-lg font-size-rg w-100" :disabled="isPush">
      <img class="icon-size-sm mr-2 pb-1" src="/images/round-get-app-w.svg" alt="">
      ユーザー情報をダウンロードする</button>
    </div>
    <div v-if="btnMode ==='timetableedit'" class="btn-group d-flex" v-on:click="timetableeditclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100" :disabled="isPush">
        登録しているユーザーのタイムテーブルを一括設定する</button>
    </div>
    <div v-if="btnMode ==='dailycalc'" class="btn-group d-flex" v-on:click="dailycalcclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100" :disabled="isPush">
        日次集計印刷</button>
    </div>
    <div v-if="btnMode ==='startwork1'" class="btn-group d-flex" v-on:click="startclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100 btncolor1" :disabled="isPush">
        作業開始</button>
    </div>
    <div v-if="btnMode ==='startwork2'" class="btn-group d-flex" v-on:click="startclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100 btncolor2" :disabled="isPush">
        作業中断</button>
    </div>
    <div v-if="btnMode ==='startwork3'" class="btn-group d-flex" v-on:click="startclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100 btncolor3" :disabled="isPush">
        作業完了</button>
    </div>
    <div v-if="btnMode ==='startwork4'" class="btn-group d-flex" v-on:click="startclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100 btncolor1" :disabled="isPush">
        作業再開</button>
    </div>
    <div v-if="btnMode ==='startworkok'" class="btn-group d-flex" v-on:click="okclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100 btncolor11" :disabled="isPush">
        OK</button>
    </div>
    <div v-if="btnMode ==='startworkcancel'" class="btn-group d-flex" v-on:click="cancelclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100 btncolor12" :disabled="isPush">
        キャンセル</button>
    </div>
    <div v-if="btnMode ==='startworksus'" class="btn-group d-flex" v-on:click="okclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100 btncolor13" :disabled="isPush">
        中断</button>
    </div>
    <div v-if="btnMode ==='startworkmiss'" class="btn-group d-flex" v-on:click="okclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100 btncolor13" :disabled="isPush">
        ミス</button>
    </div>
    <div v-if="btnMode ==='startworkcomp'" class="btn-group d-flex" v-on:click="okclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100 btncolor14" :disabled="isPush">
        完成</button>
    </div>
    <div v-if="btnMode ==='startworknext'" class="btn-group d-flex" v-on:click="okclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg w-100 btncolor15" :disabled="isPush">
        次工程</button>
    </div>
    <div v-if="btnMode ==='startsearchgo'" class="btn-group d-flex" v-on:click="searchclickBtn()">
      <button type="button" class="btn btn-primary btn-lg font-size-rg  btncolor13" :disabled="isPush">
        この条件で表示する</button>
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
      },
      isDisplay: {
          default: true,
          type: Boolean
      },
      btnName: {
          default: false,
          type: Boolean
      }
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
    copyinitclickBtn : function() {
      this.$emit('copyinitclick-event',event);
    },
    storeclickBtn : function() {
      this.$emit('storeclick-event',event);
    },
    initstoreclickBtn : function() {
      this.$emit('initstoreclick-event',event);
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
    reasonstoreclickBtn : function() {
      this.$emit('reasonstoreclick-event',event);
    },
    backclickBtn : function() {
      this.$emit('backclick-event',event);
    },
    okclickBtn : function() {
      this.$emit('okclick-event',event);
    },
    cancelclickBtn : function() {
      this.$emit('cancelclick-event',event);
    },
    listdemandclickBtn : function() {
      this.$emit('listdemandclick-event',event);
    },
    gosubdateclickBtn : function() {
      this.$emit('gosubateclick-event',event);
    },
    goadddateclickBtn : function() {
      this.$emit('goaddateclick-event',event);
    },
    csvcalcclickBtn : function() {
      this.$emit('csv-event',event);
    },
    csvsalaryclickBtn : function() {
      this.$emit('csv-event',event);
    },
    csvlogclickBtn : function() {
      this.$emit('csv-event',event);
    },
    filedownloadclickBtn : function() {
      this.$emit('filedownload-event',event);
    },
    csvshiftclickBtn : function() {
      this.$emit('csv-event',event);
    },
    passresetclickBtn : function() {
      this.$emit('passreset-event',event);
    },
    deleteclickBtn : function() {
      this.$emit('deleteclick-event',event);
    },
    usersuploadclickBtn : function() {
      this.$emit('usersupload-event',event);
    },
    usersdownloadclickBtn : function() {
      this.$emit('csv-event',event);
    },
    timetableeditclickBtn : function() {
      this.$emit('timetableedit-event',event);
    },
    dailycalcclickBtn : function() {
      this.$emit('dailycalc-event',event);
    },
    startclickBtn : function() {
      this.$emit('start-event',event);
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
