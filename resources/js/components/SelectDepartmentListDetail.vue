<template>
  <!-- リスト定義 -->
  <select class="form-control" v-model="selectedvalue" v-on:change="selChanges(selectedvalue,rowIndex)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankData" value=""></option>
    <!-- 項目設定 -->
    <option v-for="(item, index) in departmentList" v-bind:value="item.code">
      {{ item.name }}
    </option>
  </select>
</template>
<script>

import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "selecDepartmentList",
  mixins: [ dialogable, requestable ],
  props: {
    departmentList: {
        type: Array,
        default: []
    },
    blankData: {
        type: Boolean,
        default: true
    },
    placeholderData: {
        type: String,
        default: '部署を選択してください'
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
    // -------------------- 共通 ----------------------------
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
