<template>
<div>
  <!-- main contentns row -->
  <div class="row justify-content-between">
    <!-- .panel -->
    <div class="col-md pt-3">
      <div class="card shadow-pl">
      <fvl-form
        method="post"
        :data="form"
        url="/create_company_information/store"
        @success="addSuccess()"
        @error="error()"
      >
        <!-- panel header -->
        <div class="card-header clearfix bg-transparent pb-0 border-0">
          <h1 class="float-sm-left font-size-rg">会社情報</h1>
          <span class="float-sm-right font-size-sm">会社所在地や連絡先を設定できます</span>
        </div>
        <!-- /.panel header -->
        <div class="card-body pt-2">
          <!-- panel contents -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">会社名</span>
                </div>
                <fvl-input class="form-control p-0" :value.sync="form.companyName" name="companyName" />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">会社カナ</span>
                </div>
                <fvl-input class="form-control p-0" :value.sync="form.companyKana" name="companyKana" />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">郵便番号</span>
                </div>
                <fvl-input class="form-control p-0" :value.sync="form.postCode" name="postCode" />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">住所１</span>
                </div>
                <fvl-input class="form-control p-0" :value.sync="form.address1" name="address1" />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">住所２</span>
                </div>
                <fvl-input class="form-control p-0" :value.sync="form.address2" name="address2" />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">住所カナ</span>
                </div>
                <fvl-input class="form-control p-0" :value.sync="form.addressKana" name="addressKana" />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">電話番号</span>
                </div>
                <fvl-input class="form-control p-0" :value.sync="form.tell" name="tell" />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">FAX</span>
                </div>
                <fvl-input class="form-control p-0" :value.sync="form.fax" name="fax" />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">代表者氏名</span>
                </div>
                <fvl-input class="form-control p-0" :value.sync="form.representativeName" name="representativeName" />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">代表者カナ</span>
                </div>
                <fvl-input class="form-control p-0" :value.sync="form.representativeKana" name="representativeKana" />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150">E-mail</span>
                </div>
                <fvl-input class="form-control p-0" :value.sync="form.email" name="email" />
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button type="submit" class="btn btn-success btn-lg font-size-rg w-100">この内容で入力する</button>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- /.panel contents -->
        </div>
        </fvl-form>
        <!-- /panel body -->
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <!-- /main contentns row -->
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
          // alert("error");
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
