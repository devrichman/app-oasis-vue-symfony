<template>
    <SetupWrap>
        <div class="d-flex flex-column align-items-center">
            <div v-if="isValid"  class="text-center">
                <img src="@/assets/img/validation.svg" />
                <p class="text-center my-4">
                    L'email a bien été validé
                </p>
            </div>

            <div v-if="isLoading" class="text-center">
                <p class="text-center my-4">
                    Loading...
                </p>
            </div>

            <div v-if="!isLoading && !isValid"  class="text-center">
                <img src="@/assets/img/error.svg" />
                <p class="text-center my-4">
                    Lien invalide ou expiré
                </p>
            </div>
        </div>
    </SetupWrap>
</template>

<script>
    import SetupWrap from "@/components/wrappers/Setup";
    import {CONFIRM_EMAIL} from "@/graphql/security/confirm-email-mutation";

    export default {
        name: "EmailValidation",
        components: { SetupWrap },
        data() {
            return {
                isValid: false,
                isLoading: true,
            };
        },
        created() {
            if (this.$route.params.token) {
                this.$apollo.mutate( {
                    mutation: CONFIRM_EMAIL,
                    variables: {
                        token: this.$route.params.token,
                    },
                    loadingKey: 'loading',
                }).then(() => {
                    this.isValid = true;
                    this.isLoading = false;
                }).catch(() => {
                    this.isLoading = false;
                })
            }
        }
    }
</script>