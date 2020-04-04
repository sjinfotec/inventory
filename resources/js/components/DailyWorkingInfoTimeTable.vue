<template>
  <td
    class="text-left text-align-left mw-rem-4"
    v-if="calcList.holiday_description === '1日集計対象休暇'"
  >
  </td>
  <td
    class="text-left text-align-left mw-rem-4"
    v-else-if="calcList.x_positions && calcList.editor_department_code"
    style="color:#ff0000"
    data-toggle="tooltip"
    data-placement="top"
    v-bind:title="edtString"
    @mouseover="edttooltips(calcList.editor_department_name + '：' + calcList.editor_user_name,'')"
  >
    {{ calcList.working_time }}
    <img
      class="icon-size-sm svg_img orange600"
      src="/images/red_map_pin.svg"
      v-on:click="selClick()"
      alt
    />
  </td>
  <td
    class="text-left text-align-left mw-rem-4"
    v-else-if="calcList.x_positions"
  >
    {{ calcList.working_time }}
    <img
      class="icon-size-sm svg_img orange600"
      src="/images/red_map_pin.svg"
      v-on:click="selClick()"
      alt
    />
  </td>
  <td
    class="text-left text-align-left mw-rem-4"
    v-else-if="calcList.editor_department_code"
    style="color:#ff0000"
    data-toggle="tooltip"
    data-placement="top"
    v-bind:title="edtString"
    @mouseover="edttooltips(calcList.editor_department_name + '：' + calcList.editor_user_name,'')"
  >
    {{ calcList.working_time }}
  </td>
  <td
    class="text-left text-align-left mw-rem-4"
    v-else
  >{{ calcList.working_time }}
  </td>
</template>
<script>

export default {
  name: "dailyworkinginfotimetable",
  props: {
    calcList: {
      type: Object
    }
  },
  data: function() {
    return {
      edtString: "",
    };
  },
  // マウント時
  mounted() {
    console.log("dailyworkinginfotimetable  マウント");
  },
  methods: {
    // tooltips
    edttooltips: function(value1) {
      this.edtString = value1;
    },
    // セルクリックされた場合
    selClick : function() {
      this.$emit('click-event');

    }
  }
};
</script>
<style lang="scss" scoped>

.table th, .table td {
    padding: 0rem !important;
    border-style: solid dashed !important;
    border-width: 1px !important;
    border-color: #95c5ed #dee2e6 !important;
}

table {
   border-collapse: collapse !important;
   border: 1px solid #95c5ed !important;
}

.mw-rem-3 {
  min-width: 3rem;
}

.mw-rem-4 {
  min-width: 4rem;
}

</style>
