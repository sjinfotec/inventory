<template>
  <select class="form-control" v-model="selectedDepartment" v-on:change="selChanges(selectedDepartment)" placeholder="部署を選択してください">
    <option v-if="this.blankData" value=""></option>
    <option v-for="departments in departmentList" v-bind:value="departments.id">
      {{ departments.name }}
    </option>
  </select>
</template>
<script>

export default {
  name: "selectDepartment",
  props: {
    blankData: {
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
    this.getDepartmentList();
  },
  methods: {
    getDepartmentList(){
      this.$axios
        .get("/get_departments_list")
        .then(response => {
          this.departmentList = response.data;
          console.log("部署リスト取得");
        })
        .catch(reason => {
          alert("error");
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
