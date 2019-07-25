<template>
  <button class="btn btn-primary" v-on:click="downloadCSV">CSVダウンロード</button>
</template>
<script>
export default {
  name: "btnCsvDownload",
  props: {
    csvData: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      items: [{}]
    };
  },
  // マウント時
  mounted() {},
  methods: {
    downloadCSV() {
      var csv = "\ufeff" + "タイムテーブル名称,タイムテーブルNo,対象日付\n";
      this.csvData.forEach(el => {
        var line =
          el["name"] +
          "," +
          el["working_timetable_no"] +
          "," +
          el["target_date"] +
          "\n";
        csv += line;
      });
      let blob = new Blob([csv], { type: "text/csv" });
      let link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      link.download = "集計.csv";
      link.click();
    }
  }
};
</script>
