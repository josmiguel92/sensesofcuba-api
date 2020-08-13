<template>
    <button @click.stop="open" class="ml-2 btn btn-secondary btn-sm btn-icon">
        <i class="fa fa-file-pdf"></i> 
        <span class="d-none d-md-inline">{{ buttonText }}</span>
    </button>
</template>

<script>
export default {
    props: ['item'],
    computed: {
        buttonText() {
            return this.$t('general.open');
        }
    },
    methods: {
        open() {
            this.$pdfModal.show(this.item.title, this.item.file, this.$t('general.close'));
        }
    }
}
</script>

<style>

</style>