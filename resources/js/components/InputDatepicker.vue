<template>
  <datepicker 
    :value="this.defaultDate"
    :language="ja"
    :format="dateFormat"
    :clear-button="true"
    v-model="inputdate"
    v-on:opened="todaySet()"
    v-on:closed="dateselected()"
    v-on:cleared="clearDate()"
    placeholder="日付を選択してください">
  </datepicker>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import {ja} from 'vuejs-datepicker/dist/locale';

export default {
  name: "inputDatePicker",
  props: {
    defaultDate: {
      type: Date
    },
    dateFormat: {
      type: String,
      default: 'yyyy年MM月dd日'
    }
  },
  // マウント時
  mounted() {
    console.log("inputDatePicker Component mounted.");
  },
  data: function() {
    return {
      ja:ja,
      todaydate: new Date(),
      inputdate:''
    }
  },
  components: {
    Datepicker
  },
  methods: {
    // 親コンポーネントに入力値を返却
    dateselected : function() {
      console.log("dateselected = ["+ this.inputdate + ']');
      this.$emit('change-event', this.inputdate);

    },
    // TODO openedイベントが発生しない？
    todaySet() {
      console.log("todaySet = ["+ this.defaultDate + ']');
    },
    // 
    clearDate() {
      console.log("clearDate = ["+ this.inputdate + ']');
      this.$emit('change-event', this.inputdate);
    }
  }

};
</script>
