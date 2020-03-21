<template>
    <div>
        <div class="form-group">
            <input
                    type="password"
                    class="form-control"
                    ref="password"
                    :class="{'is-invalid': errors.has('password') }"
                    v-validate="{required: true, regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?\d).{8,}$/}"
                    name="password"
                    :id="`password-${id}`"
                    placeholder="Nouveau mot de passe"
                    v-model="password"/>

            <img class="form-control-icon" src="@/assets/img/password-primary.svg" />
        </div>
        <div class="form-group">
            <input
                    type="password"
                    class="form-control"
                    :class="{'is-invalid': errors.has('confirm-password') }"
                    v-validate="'required|confirmed:password'"
                    :id="`confirm-${id}`"
                    name="confirm-password"
                    placeholder="Confirmation du mot de passe"
                    v-model="confirmPassword"/>

            <img class="form-control-icon" src="@/assets/img/password-primary.svg" />
            <div v-if="errors.any()" class="col-sm-12 mt-3 text-left text-danger text-center">
                <p v-if="errors.firstByRule('password', 'regex')">
                    Le format du mot de passe n'est pas valide
                </p>
                <p v-if="errors.firstByRule('password', 'required') || errors.firstByRule('confirm-password', 'required')">
                    Tous les champs sont obligatoires
                </p>
                <p v-if="errors.firstByRule('confirm-password', 'confirmed')">
                    Les mots de passe ne correspondent pas
                </p>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: "UpdatePasswordFields",
        data() {
            return {
                password: '',
                confirmPassword: '',
            }
        },
        computed: {
            id(){
                var str = 'abcdefghijklmnopqrstuvwxyz0123456789'
                var gen = []
                for (let index = 0; index < 10; index++) {
                   gen.push(str[Math.floor(Math.random() * str.length)])
                }
                return gen.join('')
            }
        },
        created() {
            this.$validator.pause();
        },
        methods: {
            submit: function () {
                this.$validator.resume();
                this.$validator.validateAll().then(result => {
                    if(result && !this.passwordNotMatch) {
                        this.$emit('submit', this.password);
                    }
                });
            }
        }
    }
</script>