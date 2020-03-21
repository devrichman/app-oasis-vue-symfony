<template>
  <div class="wrapper wrapper--auth d-flex" :class="`auth--${page}`">
    <div class="bg-image d-flex justify-content-center">
      <img class="auth-page-logo" src="@/assets/img/logo.svg" alt />
    </div>
    <div class="auth-wrapper-content">
      <slot></slot>
    </div>
  </div>
</template>
<script>
export default {
  name: "AuthWrap",
  props:{
    page:{
      type:String,
      default:"login"
    }
  }
};
</script>