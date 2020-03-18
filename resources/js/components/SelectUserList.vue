<template>
  <!-- リスト定義 -->
  <select class="form-control" v-model="selectedvalue" v-on:change="selChanges(selectedvalue,rowIndex)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankdata" value=""></option>
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
  name: "SelecUserList",
  mixins: [ dialogable, requestable ],
  data() {
    return {
      selectedvalue: 0,
      selectedname: '',
      initlist: 0,
      itemList: [],
      blankdata: true,
      roles: []
    };
  },
  props: {
    blankData: {
        type: Boolean,
        default: true
    },
    placeholderData: {
        type: String,
        default: '氏名を選択してください'
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
    departmentValue: {
        type: String,
        default: ''
    },
    employmentValue: {
        type: String,
        default: ''
    },
    managementValue: {
        type: Number,
        default: 10
    },
    arrayRole: {
        type: Array,
        default: () => {
        return []
      }
    }
  },
  // マウント時
  mounted() {
    this.selectedvalue = this.selectedValue;
  },
  created() {
    if (this.arrayRole.length == 0) {
      this.getList(this.dateValue, this.killValue, this.initlist, this.departmentValue, this.employmentValue, this.managementValue, null);
    } else {
      this.getList(this.dateValue, this.killValue, this.initlist, this.departmentValue, this.employmentValue, this.managementValue, this.arrayRole);
    }
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
    getList(targetdate, killvalue, getdo, departmentValue, employmentValue, managementcode = null, role = null ){
      if (targetdate == '') {
        targetdate = moment(new Date()).format("YYYYMMDD");
      }
      if (getdo == '') { getdo = 1; }
      if (departmentValue == '') { departmentValue = null; }
      if (employmentValue == '') { employmentValue = null; }
      this.postRequest("/get_user_list",
        { targetdate: targetdate,
          killvalue: killvalue,
          getDo : getdo,
          departmentcode : departmentValue,
          employmentcode : employmentValue,
          managementcode : managementcode,
          roles : role
        })
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("氏名");
        });
    },
    // -------------------- 共通 ----------------------------
    // 氏名取得正常処理
    getThen(response) {
      this.itemList = [];
      var res = response.data;
      if (res.result) {
        // 固有処理 START
        this.itemList = res.details;
        if (this.addNew) {
          this.object = { name: "新規に氏名登録する", code: "" };
          this.itemList.unshift(this.object);
        }
        // 固有処理 end
      } else {
        if (res.messagedata.length > 0) {
            this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
            this.serverCatch("氏名");
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
