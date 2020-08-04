<template>
  <datepicker 
    :language="ja"
    :format="dateFormat"
    :clear-button="false"
    v-model="defaultDate"
    :placeholder="placeHolder"
    :disabled="true"
    >
  </datepicker>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import {ja} from 'vuejs-datepicker/dist/locale';

export default {
  name: "inputDatePickerDisabled",
  props: {
    defaultDate: {
      type: Date
    },
    placeHolder: {
      type: String,
      default: '日付を選択してください'
    },
    dateFormat: {
      type: String,
      default: 'yyyy年MM月dd日'
    }
  },
  data: function() {
    return {
      ja:ja,
      todaydate: new Date()
    }
  },
  components: {
    Datepicker
  }

};
</script>
