<template>
  <div class="form-group">
    <label for="business_kubun" class>営業日区分</label>
    <select
      class="form-control"
      v-model="selectedBusinessDay"
      v-on:change="selChanges(selectedBusinessDay)"
      placeholder="営業日区分を選択してください"
    >
      <option v-if="this.blankData" value></option>
      <option
        v-for="BusinessDay in BusinessDayList"
        v-bind:value="BusinessDay.code"
      >{{ BusinessDay.code_name }}</option>
    </select>
  </div>
</template>
<script>
export default {
  name: "selectedBusinessDay",
  props: {
    blankData: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      selectedBusinessDay: "",
      BusinessDayList: []
    };
  },
  // マウント時
  mounted() {
    console.log("selectedBusinessDay Component mounted." + this.blankdata);
    this.getBusinessDayList();
  },
  methods: {
    getBusinessDayList() {
      this.$axios
        .get("/get_business_day_list")
        .then(response => {
          this.BusinessDayList = response.data;
          console.log("営業区分リスト取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges: function(value) {
      console.log("selectedBusinessDay = [" + value + "]");
      this.$emit("change-event", value);
    }
  }
};
</script>
