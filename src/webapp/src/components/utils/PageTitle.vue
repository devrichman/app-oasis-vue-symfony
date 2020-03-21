<template>
  <div class="page-title-bar">
    <div class="d-flex align-items-center">
      <button class="btn mb-1 mr-2" @click="goBack">
        <img src="@/assets/img/chevron-left-primary.svg" />
      </button>
      <span class="title">{{preTitle ? preTitle + ' / ': null}} {{ title }}</span>
    </div>
  </div>
</template>
<script>
export default {
  name: "PageTitle",
  props: {
    linkToBack: {
      type: [String]
    },
    linkToBackObj: {
      type: Object
    },
    title: {
      type: String
    },
    preTitle: {
      type: String
    }
  },
  methods: {
    goBack() {
      this.$emit('back');
    }
  },
};
</script>