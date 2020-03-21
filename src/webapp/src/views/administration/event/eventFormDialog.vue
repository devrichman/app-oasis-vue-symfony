<template>
    <div class="modal d-block event-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" @click="$emit('close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <event @close="close" @update="$emit('update')" />
            </div>
        </div>
    </div>
</template>

<style>
    .modal {
        background: rgba(0, 0, 0, 0.3);
    }
    .event-dialog .modal-dialog{
        width: 70%;
        max-width: 2000px;
        height: 90%;
    }
    .event-dialog .modal-content{
        height: 100%;
        overflow-y: scroll
    }
</style>
<script>
    import Event from './Event';

    export default {
        name: "eventForm",
        components : {
            Event,
        },
        methods: {
            close() {
                this.$emit('close')
            }
        }
    }
</script>