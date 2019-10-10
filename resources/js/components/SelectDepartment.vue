<template>
  <select class="form-control" v-model="selecteddepartmentcode" v-on:change="selChanges(selecteddepartmentcode,rowIndex)" placeholder="部署を選択してください">
    <option v-if="this.blankData" value=""></option>
    <option v-for="departments in departmentList" v-bind:value="departments.code">
      {{ departments.name }}
    </option>
  </select>
</template>
<script>
import moment from "moment";

export default {
  name: "selectDepartment",
  props: {
    blankData: {
      type: Boolean,
      default: false
    },
    selectedDepartment: {
        type: String,
        default: ''
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
      selecteddepartmentcode: '',
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
      this.$axios
        .get("/get_departments_list", {
          params: {
            targetdate: datevalue
          }
        })
        .then(response => {
          this.departmentList = response.data;
        })
        .catch(reason => {
          alert("部署選択リスト作成エラー");
        });
    },
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value, index) {

        this.$emit('change-event', value, index);

    }

  }
};
</script>
