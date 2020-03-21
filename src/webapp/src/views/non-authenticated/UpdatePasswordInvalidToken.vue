<template>
  <setup-wrap>
    <div class="d-flex flex-column align-items-center">
      <div class="text-center">
        <img src="@/assets/img/error.svg" />
        <p class="text-center my-4">
          Le lien à expiré.
          <br />Veuillez retenter avec un nouveau lien.
        </p>

        <router-link to="/mot-de-passe-oublie" tag="button" class="btn btn-white-primary wide px-5">Renvoyer un lien</router-link>
      </div>
    </div>
  </setup-wrap>
</template>
<script>
import SetupWrap from "@/components/wrappers/Setup";
export default {
  components: { SetupWrap }
};
</script>