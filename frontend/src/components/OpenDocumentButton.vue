<template>
    <button @click.stop="open" class="flex-fill flex-grow-1  btn btn-secondary btn-sm btn-icon">
        <i class="fa fa-file-pdf"></i> 
        <span class="d-none d-md-inline">{{ buttonText }}</span>
    </button>
</template>

<script>
export default {
    props: ['item', 'title'],
    computed: {
        buttonText() {
            if(this.item.title !== null)
                return this.item.title;
            return this.$t('general.open');
        }
    },
    methods: {
        open() {
            let title = this.title ?? this.item.title;
            this.$pdfModal.show(title, this.item.file, this.$t('general.close'));
        }
    }
}
</script>

<style>

</style>