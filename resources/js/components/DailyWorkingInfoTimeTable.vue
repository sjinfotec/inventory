<template>
  <!-- 出勤 -->
  <td
    class="text-left text-align-left"
    v-if="calcList.x_positions && calcList.editor_department_code"
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
    class="text-left text-align-left"
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
    class="text-left text-align-left"
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
    class="text-left text-align-left"
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
