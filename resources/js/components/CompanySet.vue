<template>
  <div><span class="pr-2 d-none d-md-inline">{{ company_name }}</span></div>
</template>
<script>

export default {
  name: "CompanySet",
  data() {
    return {
      company_name: "",
      resresults: []
    };
  },
  // マウント時
  mounted() {
    this.getCompanyItem();
  },
  methods: {
    // 会社情報を取得
    getCompanyItem: function() {
      this.$axios
        .get("/get_company_info_apply", {
        })
        .then(response => {
          this.resresults = response.data;
          if (this.resresults != null) {
            for (var key in this.resresults) {
              this.company_name = this.resresults[key]['name'];
              break;
            };
          }
        })
        .catch(reason => {
          alert("会社情報取得エラー");
        });
    },
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    }
  }
};
</script>

