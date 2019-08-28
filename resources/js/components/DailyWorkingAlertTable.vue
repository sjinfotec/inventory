<template>
  <table class="table table-striped table-bordered">
    <tr>
      <th>日付</th>
      <th>雇用形態</th>
      <th>部署</th>
      <th>氏名</th>
      <th>開始時刻</th>
      <th>終了時刻</th>
      <th>警告内容</th>
    </tr>
    <tr
      v-for="(alertlist,index) in calcresults"
      :key="calclist.user_code"
      class="card-body mb-3 py-0 pt-4 border-top"
    >
      <td class="text-align-center">{{ index }}</td>
      <td class="text-align-center">{{ key }}</td>
      <td class="text-align-center">{{ value }}</td>
    </tr>
  </table>
</template>
<script>

export default {
  name: "dailyworkingalerttable",
  props: {
    alertlist: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      selectedDepartment: '',
      departmentList: []
    };
  },
  // マウント時
  mounted() {
    this.getDepartmentList('');
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
    selChanges : function(value) {

        console.log("selectdepartment = ["+ value + ']');
        this.$emit('change-event', value);

    }

  }
};
</script>
