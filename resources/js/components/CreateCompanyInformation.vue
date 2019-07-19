<template>
  <!-- panel body -->
  <div class="panel-body">
    <!-- form wrapper -->
    <fvl-form
      method="post"
      :data="form"
      url="/create_company_information/store"
      @success="addSuccess()"
      @error="error()"
    >
      <!-- Text input component -->
      <fvl-input :value.sync="form.companyName" label="会社名" name="companyName" />
      <fvl-input :value.sync="form.companyKana" label="会社カナ" name="companyKana" />
      <fvl-input :value.sync="form.postCode" label="郵便番号" name="postCode" />
      <fvl-input :value.sync="form.address1" label="住所１" name="address1" />
      <fvl-input :value.sync="form.address2" label="住所２" name="address2" />
      <fvl-input :value.sync="form.addressKana" label="住所カナ" name="addressKana" />
      <fvl-input :value.sync="form.tell" label="電話番号" name="tell" />
      <fvl-input :value.sync="form.fax" label="FAX" name="fax" />
      <fvl-input :value.sync="form.representativeName" label="代表者氏名" name="representativeName" />
      <fvl-input :value.sync="form.representativeKana" label="代表者カナ" name="representativeKana" />
      <fvl-input :value.sync="form.email" label="e-mail" name="email" />

      <!-- Submit button -->
      <fvl-submit>登録</fvl-submit>
    </fvl-form>
  </div>
</template>
<script>
import toasted from "vue-toasted";
import { FvlForm, FvlInput, FvlSubmit } from "formvuelar";

export default {
  name: "CreateCompanyInformation",
  components: {
    FvlForm,
    FvlInput,
    FvlSubmit
  },
  data() {
    return {
      form: {
        companyName: "",
        companyKana: "",
        postCode: "",
        address1: "",
        address2: "",
        addressKana: "",
        tell: "",
        fax: "",
        representativeName: "",
        representativeKana: "",
        email: ""
      },
      CompanyInfo: []
    };
  },
  // マウント時
  mounted() {
    this.getCompanyInformation();
    console.log("CreateCompanyInformation Component mounted.");
  },
  methods: {
    addSuccess() {
      // ここで会社情報呼び出す
      this.getCompanyInformation();
      this.$toasted.show("登録しました");
    },
    getCompanyInformation() {
      this.$axios
        .get("/create_company_information/get")
        .then(response => {
          this.CompanyInfo = response.data;
          this.form.companyName = this.CompanyInfo[0].name;
          this.form.companyKana = this.CompanyInfo[0].kana;
          this.form.address1 = this.CompanyInfo[0].address1;
          this.form.address2 = this.CompanyInfo[0].address2;
          this.form.addressKana = this.CompanyInfo[0].address_kana;
          this.form.postCode = this.CompanyInfo[0].post_code;
          this.form.tell = this.CompanyInfo[0].tel_no;
          this.form.fax = this.CompanyInfo[0].fax_no;
          this.form.representativeName = this.CompanyInfo[0].represent_name;
          this.form.representativeKana = this.CompanyInfo[0].represent_kana;
          this.form.email = this.CompanyInfo[0].email;
          console.log("会社情報取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    error() {
      var options = {
        position: "bottom-center",
        duration: 2000,
        fullWidth: false,
        type: "error"
      };
      this.$toasted.show("登録に失敗しました", options);
    },
    inputClear() {}
  }
};
</script>
