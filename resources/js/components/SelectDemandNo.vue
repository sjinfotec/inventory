<template>
  <select class="form-control" v-model="demandno" v-on:change="selChanges(demandno)">
    <option disabled selected style="display:none;" v-if="this.placeholderData" value="">＜{{ placeholderData }}＞</option>
    <option v-if="this.blankData" value=""></option>
    <option v-for="demands in demandList" v-bind:value="demand.no">
      {{ demand.no }}
    </option>
  </select>
</template>
<script>
import moment from "moment";

export default {
  name: "selectDemand",
  props: {
    blankData: {
        type: Boolean,
        default: false
    },
    getDo: {
        type: Number,
        default: 1
    },
    selectedDemand: {
        type: String,
        default: ''
    },
    selectedDemandno: {
        type: String,
        default: ''
    },
    placeholderData: {
        type: String,
        default: ''
    },
    dateValue: {
        type: String,
        default: ''
    }
  },
  data() {
    return {
      demand: '',
      demandno: '',
      dateApllyValue: '',
      demandList:[]
    };
  },
  // マウント時
  mounted() {
    this.demand = this.selectedDemand;
    this.demandno = this.selectedDemandno;
    if (this.dateValue == '') {
      this.dateApllyValue = moment(new Date()).format("YYYYMMDD");
    }
    this.getDemandList(this.getDo, this.selecteddemand, this.dateApllyValue, this.demandno);
  },
  methods: {
    getDemandList(getdovalue, demandvalue, datevalue, demandnovalue){
      this.demand = demandvalue;
      this.demandno = demandnovalue;
      this.demandList = [];
      this.$axios
        .get("/get_demand_list", {
          params: {
            getdo: getdovalue,
            targetdate: datevalue,
            demand: demandvalue
          }
        })
        .then(response => {
          this.demandList = response.data;
        })
        .catch(reason => {
          alert("申請番号リスト作成エラー");
        });
    },
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value) {
      this.$emit('change-event', value);

    }

  }
};
</script>
