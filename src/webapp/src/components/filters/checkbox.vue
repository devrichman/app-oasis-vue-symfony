<template>
    <div class="form-group custom-form custom-check mr-2 pl-3">
        <input type="checkbox" class="form-check-input" id="filter-checkbox"
            :value="value"
            :checked="value"
            @change="$event => $emit('input', $event.target.checked)" />
        <label class="form-check-label mb-0 font-weight-normal" for="filter-checkbox">{{ label }}</label>
    </div>
</template>
<script>
    export default {
        name: "List",
        props: {
            value: {
                required: true,
            },
            label: {
                type: String,
                required: true,
            },
            attributes: {
                type: Object,
                required: false,
            },
        },
    };
</script>
