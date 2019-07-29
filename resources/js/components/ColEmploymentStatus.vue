<template>
  <!-- col -->
  <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
    <div class="card text-white bg-secondary border-0">
      <div class="card-body px-3 py-2">
        <h1 class="font-size-sm m-0 mb-1">{{ this.itemName }}</h1>
        <p class="font-size-rg m-0">{{ this.itemValue }}</p>
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
    }
  },
  // マウント時
  mounted() {
    console.log("colemploymentstatus Component mounted.");
  }
};
</script>
