<template>
    <div class="modal d-block document-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow rounded h-100">
                <div class="modal-body mr-2 mb-2 h-100">
                    <perfect-scrollbar class="h-100">
                        <document @close="close" @submit="$emit('submit')" />
                    </perfect-scrollbar>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .modal {
        background: rgba(0, 0, 0, 0.3);
    }
    .document-dialog .modal-dialog{
        width: 60%;
        max-width: 2000px;
        height: 80%;
    }
    .modal-content {
        background-color: #f5f7fa;
    }
</style>
<script>
    import Document from './document';
    import { PerfectScrollbar } from "vue2-perfect-scrollbar";

    export default {
        name: "documentFormDialog",
        components : {
            Document,
            PerfectScrollbar
        },
        methods: {
            close() {
                this.$emit('close')
            }
        }
    }
</script>