<template>
  <!-- リスト定義 -->
  <select v-if="!isDisable" class="form-control" v-model="selectedvalue" v-on:change="selChanges(selectedvalue,rowIndex)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankData" value=""></option>
    <!-- 項目設定 -->
    <option v-for="(item, index) in itemList" v-bind:value="item.code">
      {{ item.code}}：{{ item.name }}
    </option>
  </select>
  <select v-else disabled class="form-control" v-model="selectedvalue" v-on:change="selChanges(selectedvalue,rowIndex)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankData" value=""></option>
    <!-- 項目設定 -->
    <option v-for="(item, index) in itemList" v-bind:value="item.code">
      {{ item.code}}：{{ item.name }}
    </option>
  </select>
</template>
<script>

import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "SelectDeviceList",
  mixins: [ dialogable, requestable ],
  props: {
    blankData: {
        type: Boolean,
        default: true
    },
    placeholderData: {
        type: String,
        default: '機器を選択してください'
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
    },
    isDisable: {
        type: Boolean,
        default: false
    }
  },
  data() {
    return {
      selectedvalue: 0,
      selectedname: '',
      selectedsymbol: '',
      selectedfloor_pos: '',
      itemList: []
    };
  },
    // マウント時
  mounted() {
    this.selectedvalue = this.selectedValue;
    this.getList();
  },
  methods: {
    // ------------------------ イベント処理 ------------------------------------
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value, index) {

      this.selectedname = this.getText(value);
      this.selectedsymbol = this.getSymbolText(value);
      this.selectedfloor_pos = this.getPosText(value);
      console.log('selChanges index =' + index);
      console.log('selChanges symbol =' + this.selectedsymbol);
      console.log('selChanges floor_pos =' + this.selectedfloor_pos);
      var arrayData = {'rowindex' : index, 'code' : value, 'name' : this.selectedname, 'symbol' : this.selectedsymbol, 'floor_pos' : this.selectedfloor_pos};
      this.$emit('change-event', value, arrayData);
    },
    // -------------------- サーバー処理 ----------------------------
    getList(){
      this.postRequest("/get_device_list", { code: null })
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("機器リスト");
        });
    },
    // -------------------- 共通 ----------------------------
    // 機器取得正常処理
    getThen(response) {
      this.itemList = [];
      var res = response.data;
      if (res.result) {
        // 固有処理 START
        this.itemList = res.details;
        if (this.addNew) {
          this.object = { name: "新規に機器を登録する", code: "" };
          this.itemList.unshift(this.object);
        }
        // 固有処理 end
      } else {
        if (res.messagedata.length > 0) {
            this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
            this.serverCatch("機器");
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
      var name = "";
      this.itemList.forEach(function (item) {
        if (item.code == value) {
          name = item.name;
          return name;
        }
      });
      return name;
    },
    // 選択テキスト取得
    getSymbolText : function(value) {
      var symbol = "";
      this.itemList.forEach(function (item) {
        if (item.code == value) {
          symbol = item.symbol;
          return symbol;
        }
      });
      return symbol;
    },
    // 選択テキスト取得
    getPosText : function(value) {
      var pos = "";
      this.itemList.forEach(function (item) {
        if (item.code == value) {
          pos = item.floor_pos;
          return pos;
        }
      });
      return pos;
    }

  }
};
</script>
