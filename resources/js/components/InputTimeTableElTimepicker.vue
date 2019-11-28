<template>
  <div class="input-group">
    <div class="input-group-prepend">
      <span
        class="input-group-text font-size-sm line-height-xs label-width-150"
        id="basic-addon1"
      >{{ titleData }}</span>
      <el-time-picker
        v-model="valueData"
        format='HH:mm'
        value-format='HH:mm'
        v-on:change="change()"
        size="medium "
        :placeholder="placeHolder">
      </el-time-picker>
    </div>
  </div>
</template>

<script>

export default {
  name: "inputElTimePicker",
  props: {
    titleData: {
      default: '',
      type: String
    },
    placeHolder: {
        default: '時刻を入力します',
        type: String
    },
    valueData: {
        default: '',
        type: String
    }
  },
  methods: {
    // 親コンポーネントに入力値を返却
    change : function() {
      this.$emit('change-event', this.valueData);

    }
  }

};
</script>
<style scoped>
  .time-w-100 {
    width: 150px;
    height: 100px;
  }
</style>
