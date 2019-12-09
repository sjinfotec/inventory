<template>
  <div>
    <el-time-picker
      v-model="valueData"
      format='HH:mm'
      value-format='HH:mm'
      v-on:change="change()"
      v-on:focus="focus()"
      size="medium"
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
    change : function($event) {
      this.$emit('change-event', this.valueData);

    },
    focus : function($event) {
      console.log('this.value = '+this.value);
      console.log('this.value = '+$event.value);
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
