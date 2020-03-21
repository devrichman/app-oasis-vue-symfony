<template>
  <div class="wrapper wrapper--setup d-flex flex-column">
    <div class="bg-image d-flex align-items-start justify-content-center">
      <img class="setup-page-logo" src="@/assets/img/logo.svg" alt />
    </div>
    <div class="setup-page d-flex flex-column flex-grow-1" >
      <div class="setup-page-content">
        <slot></slot>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "SetupWrap",
  computed: {
    bgImage() {
      var img = require("../../assets/img/bg-full.png");
      return `background-image: url(${img})`;
    },
    bgImageAlt() {
      var img = require("../../assets/img/fond-gris.svg");
      return `background-image: url(${img})`;
    }
  }
};
</script>