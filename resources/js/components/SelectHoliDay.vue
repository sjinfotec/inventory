<template>
  <div class="form-group">
    <label for="business_kubun" class>休暇区分</label>
    <select
      class="form-control"
      v-model="selectedHoliDay"
      v-on:change="selChanges(selectedHoliDay)"
      placeholder="休暇区分を選択してください"
    >
      <option v-if="this.blankData" value></option>
      <option v-for="Holiday in HoliDayList" v-bind:value="Holiday.code">{{ Holiday.code_name }}</option>
    </select>
  </div>
</template>
<script>
export default {
  name: "selectedHoliDay",
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
      selectedHoliDay: "",
      HoliDayList: []
    };
  },
  // マウント時
  mounted() {
    console.log("selectedHoliDay Component mounted." + this.blankdata);
    this.getHoliDayList();
  },
  methods: {
    getHoliDayList() {
      this.$axios
        .get("/get_holi_day_list", {})
        .then(response => {
          this.HoliDayList = response.data;
          console.log("休暇区分リスト取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges: function(value) {
      console.log("selectedHoliDay = [" + value + "]");
      this.$emit("change-event", value);
    }
  }
};
</script>
