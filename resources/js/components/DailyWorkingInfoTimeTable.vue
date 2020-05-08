<template>
  <td
    class="tableClass"
    v-if="calcList.holiday_description === '1日集計対象休暇'"
  >
  </td>
  <td
    class="tableClass"
    v-else-if="menuData[userIndex]['is_select'] === 0 && menuData[userconIndex]['is_select'] === 0"
    data-toggle="tooltip"
    data-placement="top"
    v-bind:title="edtString"
  >
    {{ calcList.working_time }}
  </td>
  <td
    class="tableClass"
    v-else-if="menuData[userIndex]['is_select'] === 1 && calcList.x_positions && calcList.editor_department_code"
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
    class="tableClass"
    v-else-if="menuData[userIndex]['is_select'] === 1 && !calcList.x_positions && calcList.editor_department_code"
    style="color:#ff0000"
    data-toggle="tooltip"
    data-placement="top"
    v-bind:title="edtString"
    @mouseover="edttooltips(calcList.editor_department_name + '：' + calcList.editor_user_name,'')"
  >
    {{ calcList.working_time }}
  </td>
  <td
    class="tableClass"
    v-else-if="menuData[userIndex]['is_select'] === 1 && calcList.x_positions && !calcList.editor_department_code"
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
    class="tableClass"
    v-else-if="menuData[userIndex]['is_select'] === 1 && !calcList.x_positions && !calcList.editor_department_code"
  >
    {{ calcList.working_time }}
  </td>
  <td
    class="tableClass"
    v-else-if="menuData[userconIndex]['is_select'] === 1 && calcList.x_positions && calcList.editor_department_code && accountData === ssjjooId && loginUser === edituserId"
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
    class="tableClass"
    v-else-if="menuData[userconIndex]['is_select'] === 1 && !calcList.x_positions && calcList.editor_department_code && accountData === ssjjooId && loginUser === edituserId"
    style="color:#ff0000"
    data-toggle="tooltip"
    data-placement="top"
    v-bind:title="edtString"
    @mouseover="edttooltips(calcList.editor_department_name + '：' + calcList.editor_user_name,'')"
  >
    {{ calcList.working_time }}
  </td>
  <td
    class="tableClass"
    v-else-if="menuData[userconIndex]['is_select'] === 1 && calcList.x_positions && !calcList.editor_department_code && accountData === ssjjooId && loginUser === edituserId"
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
    class="tableClass"
    v-else-if="menuData[userconIndex]['is_select'] === 1 && !calcList.x_positions && !calcList.editor_department_code && accountData === ssjjooId && loginUser === edituserId"
  >
    {{ calcList.working_time }}
  </td>
  <td
    class="tableClass"
    v-else
  >
    {{ calcList.working_time }}
  </td>
</template>
<script>

// CONST
const C_USER_INDEX = 26;
const C_USER_CON_INDEX = 27;
const C_SSJJOO_ID = 'SSJJOO00';

export default {
  name: "dailyworkinginfotimetable",
  props: {
    calcList: {
      type: Object
    },
    loginUser: {
      type: String,
      default: ""
    },
    loginRole: {
      type: String,
      default: ""
    },
    accountData: {
      type: String,
      default: ""
    },
    menuData: {
      type: Array,
      default: []
    },
    userIndex: {
      type: Number,
      default: 0
    },
    userconIndex: {
      type: Number,
      default: 0
    },
    ssjjooId: {
      type: String,
      default: ""
    },
    edituserId: {
      type: String,
      default: ""
    },
    tableClass: {
      type: String,
      default: "text-left text-align-left mw-rem-4"
    }
  },
  data: function() {
    return {
      edtString: ""
    };
  },
  computed: {
    userindex: function() {
      return C_USER_INDEX;
    },
    userconindex: function() {
      return C_USER_CON_INDEX;
    },
    ssjjoo_id: function() {
      return C_SSJJOO_ID;
    }
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
