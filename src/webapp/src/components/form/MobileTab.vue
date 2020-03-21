<template>
  <div class="mobile-tab" :class="{'active':active}">
    <div class="mobile-tab--title" @click="$emit('toggle',index)">
      <div class="d-flex align-items-center">
        <!-- <i class="fa" :class="title.icon" /> -->
        <img
          class="nav-icon"
          :src="require(`@/assets/img/${imgs[title.icon]}${ active ? '-active' : ''}.svg`)"
        />
        <span class="nav-title">{{title.name}}</span>
        <div class="ml-auto">
            <img :class="{'active-arrow':active}" src="@/assets/img/arrow-right-primary.svg" alt="">
        </div>
      </div>
    </div>
      <div class="mobile-tab--body" :class="{'d-block':active, 'd-none':!active}">
        <slot></slot>
      </div>
  </div>
</template>
<script>
export default {
  name: "MobileTab",
  props: {
    title: {
      type: Object,
      required: true
    },
    active: {
      type: Boolean,
      default: false
    },
    index:{
        type:Number,
        required: true,
    }
  },
  data() {
    return {
      imgs: {
        "fa-tag": "roles",
        "fa-users": "users",
        "fa-building": "companies",
        "fa-calendar-alt": "companies",
        "fa-clipboard": "programs"
      }
    };
  }
};
</script>