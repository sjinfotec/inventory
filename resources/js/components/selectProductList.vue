<template>
  <!-- リスト定義 -->
  <select v-if="!isDisable" class="form-control" v-model="selectedvalue" v-on:change="selChanges(selectedvalue,rowIndex)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankData" value=""></option>
    <!-- 項目設定 -->
    <option v-for="(item, index) in itemList" v-bind:value="item.code">
      {{ item.name }}
    </option>
  </select>
  <select v-else disabled class="form-control" v-model="selectedvalue" v-on:change="selChanges(selectedvalue,rowIndex)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankData" value=""></option>
    <!-- 項目設定 -->
    <option v-for="(item, index) in itemList" v-bind:value="item.code">
      {{ item.name }}
    </option>
  </select>
</template>
<script>

import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "selectProductList",
  mixins: [ dialogable, requestable ],
  props: {
    blankData: {
        type: Boolean,
        default: true
    },
    placeholderData: {
        type: String,
        default: '品名を選択してください'
    },
    selectedValue: {
        type: Number,
        default: ''
    },
    addNew: {
        type: Boolean,
        default: false
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
      itemList: []
    };
  },
    // マウント時
  mounted() {
    this.selectedvalue = this.selectedValue;
    this.getList(this.selectedValue, null, '1');
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
    getList(targetcode, processescode, distinctkbn){
      var arrayParams = { code: null, processes_code: null, distinct_kbn: distinctkbn };
      this.postRequest("/get_product_list", arrayParams)
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("品名");
        });
    },
    // -------------------- 共通 ----------------------------
    // 品名所取得正常処理
    getThen(response) {
      this.itemList = [];
      var res = response.data;
      console.log('selecProductList getThen = res.result' + res.result);
      if (res.result) {
        // 固有処理 START
        console.log('selecProductList getThen = res.details' + res.details.length);
        this.itemList = res.details;
        if (this.addNew) {
          this.object = { name: "新規に品名を登録する", code: "" };
          this.itemList.unshift(this.object);
        }
        // 固有処理 end
      } else {
        if (res.messagedata.length > 0) {
            this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
            this.serverCatch("品名");
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
          name = item.name;
          return name;
        }
      });
      return name;
    }

  }
};
</script>
