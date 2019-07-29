<template>
  <div>
    <!-- col -->
    <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
      <div class="card text-white bg-secondary border-0">
        <div class="card-body px-3 py-2">
          <h1 class="font-size-sm m-0 mb-1">雇用形態</h1>
          <p class="font-size-rg m-0">{{ this.employmentstatusname }}</p>
        </div>
      </div>
    </div>
    <!-- /.col -->
    <!-- col -->
    <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
      <div class="card text-white bg-secondary border-0">
        <div class="card-body px-3 py-2">
          <h1 class="font-size-sm m-0 mb-1">勤務時間</h1>
          <p class="font-size-lg m-0">{{ this.totalworkingtimes }}</p>
        </div>
      </div>
    </div>
    <!-- /.col -->
    <!-- col -->
    <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
      <div class="card text-white bg-secondary border-0">
        <div class="card-body px-3 py-2">
          <h1 class="font-size-sm m-0 mb-1">勤務状態</h1>
          <p class="font-size-rg m-0">{{ this.workingstatusname }}</p>
        </div>
      </div>
    </div>
    <!-- /.col -->
    <!-- col -->
    <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
      <div class="card text-white bg-secondary border-0">
        <div class="card-body px-3 py-2">
          <h1 class="font-size-sm m-0 mb-1">勤務シフト</h1>
          <p class="font-size-rg m-0">{{ this.workingtimetablename }}</p>
        </div>
      </div>
    </div>
    <!-- /.col -->
  </div>
</template>
<script>

export default {
<<<<<<< HEAD
  name: "ColMissingMiddle",
=======
  name: "ColEmploymentStatus",
>>>>>>> develop
  props: {
    employmentstatusname: {
        type: String,
        default: null
    },
    totalworkingtimes: {
        type: String,
        default: null
    },
    workingstatusname: {
        type: String,
        default: null
    },
    workingtimetablename: {
        type: String,
        default: null
    },
    displaynone: {
        type: Boolean,
        default: true
    }
  }
};
</script>
