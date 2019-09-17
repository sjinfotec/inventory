<template>
  <!-- col -->
  <div class="col-12 pb-2 align-self-stretch">
    <!-- <div v-if="this.itemControl === 'OK'">
      <div class="card text-success border border-success">
        <div class="card-body px-3 py-2">
          <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-error-b.svg" alt=""></span>
          <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
          <p v-for="value in itemNote" class="font-size-sm my-2">{{ value }}</p>
        </div>
      </div>
    </div> -->
    <div v-if="this.itemControl === 'WA'">
    <!-- <div v-else-if="this.itemControl === 'WA'"> -->
      <div class="card text-warning border border-warning">
        <div class="card-body px-3 py-2">
          <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-error-b.svg" alt=""></span>
          <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
          <p v-for="value in itemNote" class="font-size-sm my-2">{{ value }}</p>
        </div>
      </div>
    </div>
    <div v-if="this.itemControl === 'NG'">
    <!-- <div v-else> -->
      <div class="card text-danger border border-danger">
        <div class="card-body px-3 py-2">
          <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-error-b.svg" alt=""></span>
          <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
          <p v-for="value in itemNote" class="font-size-sm my-2">{{ value }}</p>
        </div>
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
    itemControl: {
        type: String,
        default: null
    },
    itemNote: {
        type: Array
    }
  },
  data() {
    return {
      itemValue:''
    };
  }
};
</script>
