<template>
  <!-- リスト定義 -->
  <select class="form-control" v-model="selectedvalue" v-on:change="selChanges(selectedvalue,rowIndex)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankData" value=""></option>
    <!-- 項目設定 -->
    <option v-for="(item, index) in itemList" v-bind:value="item.XXXX">
      {{ item.XXXX }}
    </option>
  </select>
</template>
<script>

import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "selecXXXXXXXXList",
  mixins: [ dialogable, requestable ],
  props: {
    blankData: {
        type: Boolean,
        default: true
    },
    placeholderData: {
        type: String,
        default: 'ＸＸＸＸＸＸを選択してください'
    },
    selectedValue: {
        type: Number,
        default: ''
    },
    addNew: {
        type: Boolean,
        default: false
    },
    getDo: {
        type: Number,
        default: 1
    },
    dateValue: {
        type: String,
        default: ''
    },
    killValue: {
        type: Boolean,
        default: false
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
      this.postRequest("/XXXXXXXXXX_list", { targetdate: targetdate, killvalue: this.killValue })
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("ＸＸ");
        });
    },
    // -------------------- 共通 ----------------------------
    // ユーザー取得正常処理
    getThen(response) {
      this.itemList = [];
      var res = response.data;
      if (res.result) {
          // 固有処理 START
          this.itemList = res.details;
          if (this.itemList.length != 0) {
            if (this.addNew) {
              this.object = { XXXX: "新規にＸＸＸＸＸＸＸＸを登録する", XXXX: "" };
              this.itemList.unshift(this.object);
            }
          } else {
            this.placeholderData = "はじめに「所定就業時間の登録」を選択してください"
            this.object = { XXXX: "所定就業時間の登録", XXXX: "" };
            this.itemList.unshift(this.object);
          }
          // 固有処理 end
      } else {
          if (res.messagedata.length > 0) {
              this.messageswal("エラー", res.messagedata, "error", true, false, true);
          } else {
              this.serverCatch("部署");
          }
      }
    },
    // 異常処理
    serverCatch(kbn) {
      var messages = [];
      messages.push(kbn + "選択リスト作成に失敗しました");
      this.messageswal("エラー", messages, "error", true, false, true);
    },
    // 選択テキスト取得
    getText : function(value) {
      name = "";
      this.itemList.forEach(function (item) {
        if (item.XXXX == value) {
          name = item.XXXX;
          return name;
        }
      });
      return name;
    }

  }
};
</script>
