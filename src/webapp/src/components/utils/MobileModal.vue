<template>
  <div class="custom-modal" :class="{'active':active}">
    <div class="custom-modal--wrap">
      <div class="card custom-modal--card">
        <div class="d-flex p-2">
          <button class="btn ml-auto" @click="closeModal">
            <img src="@/assets/img/croix.svg" alt />
          </button>
        </div>
        <div class="card-body pt-0">
          <slot />
        </div>
      </div>
    </div>
    <div class="custom-modal--backdrop" @click="closeModal" />
  </div>
</template>'
<script>
export default {
  name: "MobileModal",
  props: {
    active: {
      type: Boolean,
      default: false
    }
  },
  methods: {
      closeModal(){
          this.$emit('close')
      }
  },
};
</script>