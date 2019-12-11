<template>
  <select class="form-control" v-model="selecteddepartmentcode" v-on:change="selChanges(selecteddepartmentcode,rowIndex)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankData" value=""></option>
    <option v-for="departments in departmentList" v-bind:value="departments.code">
      {{ departments.name }}
    </option>
  </select>
</template>
<script>
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {requestable} from '../mixins/requestable.js';


export default {
  name: "selectDepartment",
  mixins: [ dialogable, requestable ],
  props: {
    blankData: {
      type: Boolean,
      default: false
    },
    placeholderData: {
        type: String,
        default: '部署を選択してください'
    },
    selectedDepartment: {
        type: String,
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
      selecteddepartmentcode: '',
      selectedname: '',
      dateApllyValue: '',
      departmentList: []
    };
  },
  // マウント時
  mounted() {
    this.selecteddepartmentcode = this.selectedDepartment;
    if (this.dateValue == '') {
      this.dateApllyValue = moment(new Date()).format("YYYYMMDD");
    }
    this.getDepartmentList(this.dateApllyValue);
  },
  methods: {
    getDepartmentList(datevalue){
      this.postRequest("/get_departments_list", { targetdate: datevalue, killvalue: this.killValue })
        .then(response  => {
          this.departmentList = response.data;
          if (this.addNew) {
            this.object = { name: "新規に部署を登録する", code: "" };
            this.departmentList.unshift(this.object);
          }
        })
        .catch(reason => {
          var messages = [];
          messages.push("部署選択リスト作成エラー");
          this.messageswal("エラー", messages, "error", true, false, true);
        });
    },
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value, index) {

      this.selectedname = this.getText(value);
      var arrayData = {'rowindex' : index, 'name' : this.selectedname};
      this.$emit('change-event', value, arrayData);
    },
    // 選択テキスト取得
    getText : function(value) {
      name = "";
      this.departmentList.forEach(function (item) {
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
