<template>
  <auth-wrap page="conditions">
    <div class="auth-form-wrap">
      <form class="form-signin">
        <div class>
          <div class="text-center my-3">
            <p
              v-show="displaySuccess"
              class="alert alert-success"
            >Success. Redirecting to dashboard.</p>
            <p v-show="acceptError" class="alert alert-danger">An error occured.</p>
          </div>
          <div class="conditions-card">
            <h5 class="text-uppercase mx-4 mb-4">Conditions générales de service</h5>
            <perfect-scrollbar style="height:315px">
              <p>
                You agree that by clicking “Join Now”, “Join Oasys”, “Sign Up” or similar, registering, accessing or using our services (described below), you are agreeing to enter into a legally binding contract with LinkedIn (even if you are using our Services on behalf of a company). If you do not agree to this contract (“Contract” or “User Agreement”), do not click “Join Now” (or similar) and do not access or otherwise use any of our Services. If you wish to terminate this contract, at any time you can do so by closing your account and no longer accessing or using our Services.
                Services
                This Contract applies to Oasys.com, apps, Slideshare, LinkedIn Learning and other LinkedIn-related sites, apps, communications and other services that state that they are offered under this Contract (“Services”), including the offsite collection of data for those Services, such as our ads and the “Apply with LinkedIn” and “Share with LinkedIn” plugins. Registered users of our Services are “Members” and unregistered users are “Visitors”.
                You are entering into this Contract with LinkedIn (also referred to as “we” and “us”).
                We use the term “Designated Countries” to refer to countries in the European Union (EU), European Economic Area (EEA), and Switzerland.
                If you reside in the “Designated Countries”, you are entering into this Contract with LinkedIn Ireland Unlimited Company (“LinkedIn Ireland”) and LinkedIn Ireland will be the controller of your personal data provided to, or collected by or for, or processed in connection with our Services.
                If you reside outside of the “Designated Countries”, you are entering into this Contract with LinkedIn Corporation (“LinkedIn Corp.”) and LinkedIn Corp. will be the controller of your personal data provided to, or collected by or for, or processed in connection with our Services.
                This Contract applies to Members and Visitors.
                As a Visitor or Member of our Services, the collection, use and sharing of your personal data is subject to this Privacy Policy (which includes our Cookie Policy and other documents referenced in this Privacy Policy) and updates.
              </p>
            </perfect-scrollbar>
          </div>
        </div>

        <div class="btn-wrap mt-4 d-flex">
          <a href="/#/connexion" class="btn btn-white-secondary wide bg-white">Refuser</a>
          <button
            class="btn btn-white-primary wide"
            @click.prevent="submit"
          >Accepter</button>
        </div>
      </form>
    </div>
  </auth-wrap>
</template>

<script>
import { PerfectScrollbar } from "vue2-perfect-scrollbar";
import {LOGGED_USER} from "@/graphql/security/logged-user-query";
import {ACCEPT_CGU} from "@/graphql/security/accept-cgu-mutation";

export default {
  name: "AcceptCgu",
  components: {
    PerfectScrollbar
  },
  data() {
    return {
      cguAccepted: false,
      displaySuccess: false,
      acceptError: false,
      router: this.$router
    };
  },
  apollo: {
    me: {
      query: LOGGED_USER
    }
  },
  methods: {
    submit() {
      this.$apollo.mutate({
          mutation: ACCEPT_CGU,
          update(cache) {
              const data = cache.readQuery({query: LOGGED_USER});
              data.me.cguAccepted = true;
              cache.writeQuery({query: LOGGED_USER, data})
          }
        }).then(res => {
          this.displaySuccess = true;
          if (this.me.type === 'admin') {
            this.$router.push({ name: 'Users' });
          } else {
            this.$router.push({ name: 'Me' });
          }
        });
    }
  }
};
</script>

<style scoped>
</style>
