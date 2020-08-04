<template>
  <!-- リスト定義 -->
  <select class="form-control" v-model="selectedvalue" v-on:change="selChanges(selectedvalue,rowIndex)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankData" value=""></option>
    <!-- 項目設定 -->
    <option v-for="(item, index) in itemList" v-bind:value="item.code">
      {{ item.code_name }}
    </option>
  </select>
</template>
<script>

import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "selectGeneralList",
  mixins: [ dialogable, requestable ],
  props: {
    blankData: {
        type: Boolean,
        default: true
    },
    placeholderData: {
        type: String,
        default: '区分を選択してください'
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
    },
    identificationId: {
        type: String,
        default: ''
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
    this.getList('');
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
      this.postRequest("/get_general_list", { identificationid: this.identificationId })
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("");
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
          // 固有処理 end
      } else {
          if (res.messagedata.length > 0) {
              this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
          } else {
              this.serverCatch("");
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
        if (item.code == value) {
          name = item.code_name;
          return name;
        }
      });
      return name;
    }
  }

};
</script>
