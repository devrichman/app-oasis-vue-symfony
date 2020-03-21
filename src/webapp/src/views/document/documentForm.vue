<template>
    <dash-wrap article="Liste de" title="Documents" mode="folders">
        <document />
    </dash-wrap>
</template>
<script>
import Document from './document';

export default {
    name: "documentForm",
    components : {
        Document,
    },
    loadingKey: "loading"
}
</script>