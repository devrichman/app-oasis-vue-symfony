<template>
  <div class="single-user" :class="{'action': hasAction}" >
    <div class="profile-picture" :class="{'deletable':hasAction}">
      <img class="main-profile" v-if="user.profilePicture" :src="getImageUrl(user.profilePicture)" />
      <img class="main-profile" v-else src="@/assets/img/user-primary.svg" />
      <div class="delete-icon-wrap" v-if="hasAction">
        <button type="button" class="btn p-0" @click="$emit('click')">
          <img
            class="delete-icon"
            src="@/assets/img/trash.svg"
            :class="actionIcon"
            v-if="hasAction"
          />
        </button>
      </div>
    </div>
    <span class="user-name">{{ user.firstName }} {{ user.lastName }}</span>
  </div>
</template>

<script>
import {FILE_PATH} from "@/enum/FilePathConstant";

export default {
  name: "SingleUser",
  props: {
    user: {
      type: Object,
      required: true
    },
    hasAction: {
      type: Boolean,
      default: false
    },
    actionIcon: {
      type: String,
      default: "fa-times-circle"
    }
  },
  data: () => ({}),
  computed: {},
  methods: {
    getImageUrl(profilePhoto) {
      let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
        0,
        process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
      );
      return baseUrl + FILE_PATH + profilePhoto.id;
    }
  }
};
</script>