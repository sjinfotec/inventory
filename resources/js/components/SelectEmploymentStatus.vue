<template>
  <select class="form-control" v-model="selectedEmploymentStatus" v-on:change="selChanges(selectedEmploymentStatus)" placeholder="雇用形態を選択してください">
    <option v-if="this.blankData" value=""></option>
    <option v-for="employmentstatus in employmentstatuslist" v-bind:value="employmentstatus.code">
      {{ employmentstatus.code_name }}
    </option>
  </select>
</template>
<script>

export default {
  name: "selectEmploymentStatus",
  props: {
    blankData: {
        type: Boolean,
        default: false
    },
    getDo: {
        type: Number,
        default: 0
    }
  },
  data() {
    return {
      selectedEmploymentStatus:'',
      employmentstatuslist:[]
    };
  },
  // マウント時
  mounted() {
    this.getemploymentstatus(this.getDo, '');
  },
  methods: {
    getemploymentstatus(getdovalue, value){
      this.$axios
        .get("/get_employment_status_list", {
        })
        .then(response => {
          this.employmentstatuslist = response.data;
        })
        .catch(reason => {
          alert("雇用形態選択リスト作成エラー");
        });
    },
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value) {

        this.$emit('change-event', value);

    }

  }
};
</script>
