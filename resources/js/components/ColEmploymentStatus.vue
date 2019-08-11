<template>
  <!-- col -->
  <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
    <div class="card text-secondary border border-secondary">
      <div class="card-body px-3 py-2">
        <h1 class="font-size-sm m-0 mb-1">{{ this.itemName }}</h1>
        <p class="font-size-rg m-0">{{ this.value }}</p>
      </div>
    </div>
  </div>
  <!-- /.col -->
</template>
<script>

export default {
  name: "colemploymentstatus",
  props: {
    itemName: {
        type: String,
        default: null
    },
    itemValue: {
        type: String,
        default: null
    },
    itemsecoundValue: {
        type: String,
        default: null
    }
  },
  data() {
    return {
      value:'　',
      str1:'',
      str2:''
    };
  },
  // マウント時
  mounted() {
    this.edtValue();
  },
  methods: {
    edtValue(){
      if(this.itemValue != null && this.itemValue != '' && this.itemValue != '　'){
        this.value = this.itemValue;
      } else {
        if(this.itemsecoundValue != null && this.itemsecoundValue != '' && this.itemsecoundValue != '　'){
          this.value = this.itemsecoundValue;
        }
      }
    }
  }
};
</script>
