<template>
  <div>
    <el-time-picker
      v-model="valueData"
      format='HH:mm'
      value-format='HH:mm'
      v-on:change="change()"
      size="medium "
      :placeholder="placeHolder">
    </el-time-picker>
  </div>
</template>

<script>

export default {
  name: "inputElTimePicker",
  props: {
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
