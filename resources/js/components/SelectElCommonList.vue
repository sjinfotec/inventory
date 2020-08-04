<template>
  <el-select v-model="selectedvalue" filterable :placeholder="placeholderData">
    <el-option></el-option>
    <el-option
      v-for="item in options"
      size="large"
      :key="item.value"
      :label="item.label"
      :value="item.value">
    </el-option>
  </el-select>
</template>
<script>

export default {
  name: "SelectElCommonLList",
  props: {
    placeholderData: {
        type: String,
        default: '選択してください'
    },
    blankData: {
        type: Boolean,
        default: true
    },
    selectedValue: {
        type: Number,
        default: ''
    },
    optionListname: {
        type: String,
        default: ''
    }
  },
  data() {
    return {
      selectedvalue: '',
      selectedname: '',
      options: [],
      getUrl: ''
    };
  },
  // マウント時
  mounted() {
    this.selectedvalue = this.selectedValue;
    this.getOptionList();
  },
  methods: {
    getOptionList(){
      if (this.optionListname === "working_timetables") {
        this.getUrl = "/get_time_table_list";
      }
      this.$axios
        .get(this.getUrl, {
          params: {
          }
        })
        .then(response => {
          this.options = response.data;
        })
        .catch(reason => {
          alert("リスト作成に失敗しました。");
        });
    },
    // 選択が変更された場合、親コンポーネントに選択値を返却
    selChanges : function(value) {
      this.selectedname = this.getText(value);
      this.$emit('change-event', value, this.selectedname);

    },
    // 選択テキスト取得
    getText : function(value) {
      name = "";
      this.options.forEach(function (item) {
        if (item.code === value) {
          name = item.code_name;
          return name;
        }
      });
      return name;
    }

  }
};
</script>
