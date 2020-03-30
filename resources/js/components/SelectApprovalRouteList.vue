<template>
  <!-- リスト定義 -->
  <select class="form-control" v-model="selectedvalue" v-on:change="selChanges(selectedvalue,rowIndex)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankData" value=""></option>
    <!-- 項目設定 -->
    <option v-for="(item, index) in itemList" v-bind:value="item.no">
      {{ item.name }}
    </option>
  </select>
</template>

<script>

import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "selectimetableList",
  mixins: [ dialogable, requestable ],
  props: {
    blankData: {
        type: Boolean,
        default: true
    },
    placeholderData: {
        type: String,
        default: '承認者ルートを選択してください'
    },
    selectedValue: {
        type: Number,
        default: ''
    },
    addNew: {
        type: Boolean,
        default: false
    },
    dateValue: {
        type: String,
        default: ''
    },
    rowIndex: {
        type: Number,
        default: 0
    }
  },
  data() {
    return {
      selectedvalue: 0,
      selectedname: '',
      itemList: []
    };
  },
    // マウント時
  mounted() {
    this.selectedvalue = this.selectedValue;
    this.getList(this.dateValue);
  },
  methods: {
    // ------------------------ イベント処理 ------------------------------------
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value, index) {
      this.selectedname = this.getText(value);
      var arrayData = {'rowindex' : index, 'name' : this.selectedname};
      this.$emit('change-event', value, arrayData);
    },
    // -------------------- サーバー処理 ----------------------------
    getList(targetdate){
      if (targetdate == '') {
          targetdate = moment(new Date()).format("YYYYMMDD");
      }
      this.postRequest("/approval_root_list", { })
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("承認者ルート");
        });
    },
    // -------------------- 共通 ----------------------------
    // 承認者ルート取得正常処理
    getThen(response) {
      this.itemList = [];
      var res = response.data;
      if (res.result) {
          // 固有処理 START
          this.itemList = res.details;
          if (this.itemList.length != 0) {
            if (this.addNew) {
              this.object = { apply_term_from: "", name: "新規に承認者ルートを登録する", no: "" };
              this.itemList.unshift(this.object);
            }
          } else {
            var messages = [];
            messages.push("承認者ルートが設定されていません。");
            messages.push("新規に承認者ルートを登録してください。");
            this.htmlMessageSwal("確認", messages, "info", true, false);
            this.object = { apply_term_from: "", name: "新規に承認者ルートを登録する", no: "" };
            this.itemList.unshift(this.object);
          }
          // 固有処理 end
      } else {
          if (res.messagedata.length > 0) {
              this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
          } else {
              this.serverCatch("承認者ルート");
          }
      }
    },
    // 異常処理
    serverCatch(kbn) {
      var messages = [];
      messages.push(kbn + "選択リスト作成に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    // 選択テキスト取得
    getText : function(value) {
      name = "";
      this.itemList.forEach(function (item) {
        if (item.no == value) {
          name = item.name;
          return name;
        }
      });
      return name;
    }

  }
};
</script>
