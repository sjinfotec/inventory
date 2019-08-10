<template>
  <!-- col -->
  <div class="col-12 pb-2 align-self-stretch">
    <div class="card text-danger border border-danger">
      <div class="card-body px-3 py-2">
        <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-error-b.svg" alt=""></span>
        <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
        <p class="font-size-sm my-2">{{ this.itemValue }}</p>
      </div>
    </div>
  </div>
  <!-- /.col -->
</template>
<script>

export default {
  name: "colnote",
  props: {
    itemName: {
        type: String,
        default: null
    },
    itemNote: {
        type: String,
        default: null
    },
    itemLate: {
        type: String,
        default: null
    },
    itemLeaveearly: {
        type: String,
        default: null
    }
  },
  data() {
    return {
      itemValue:''
    };
  },
  // マウント時
  mounted() {
    this.edtNote();
  },
  methods: {
    edtNote(){
      this.itemValue = '';
      if(this.itemNote != null && this.itemNote != ''){
        this.itemValue = this.itemNote;
      }
      if(this.itemLate == '1'){
        if(this.itemValue != ''){
          this.itemValue += '、';
        }
        this.itemValue += '遅刻';
      }
      if(this.itemLeaveearly == '1'){
        if(this.itemValue != ''){
          this.itemValue += '、';
        }
        this.itemValue += '早退';
      }
    }
  }
};
</script>
