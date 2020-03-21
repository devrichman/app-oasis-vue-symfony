<template>
    <div class="modal d-block" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ title }}</h5>
                    <button type="button" class="close" @click="$emit('close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" v-if="description">
                    <p v-html="description" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-consultant-light" @click="$emit('close')">Annuler</button>
                    <button type="button" class="btn btn-gradient-primary" @click="$emit('confirm')">Confirmer</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: "ConfirmDialog",
        props: {
            title: {
                type: String,
                required: true,
            },
            description: {
                type: String,
            }
        },
    };
</script>
<style>
    .modal {
        background: rgba(0, 0, 0, 0.3);
    }
</style>