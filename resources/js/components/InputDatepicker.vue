<template>
  <datepicker :value="this.defaultDate" :language="ja" :format="DatePickerFormat" v-model="inputdate" v-on:closed="dateselected()" placeholder="日付を選択してください"></datepicker>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import {ja} from 'vuejs-datepicker/dist/locale';

export default {
  name: "inputDatePicker",
  props: {
    defaultDate: {
      type: Date
    }
  },
  // マウント時
  mounted() {
    console.log("inputDatePicker Component mounted." + this.defaultDate);
  },
  data: function() {
    return {
      ja:ja,
      DatePickerFormat: 'yyyy/MM/dd',
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

    }
  }

};
</script>
