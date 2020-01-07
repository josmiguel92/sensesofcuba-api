<template>
    <div class="media p-2 bg-secondary">
        <img :src="`../${item.image}`" class="img-fluid mr-3" alt="">
        <div class="media-body d-flex flex-column h-100 pt-2 pb-2">
            <h5 class="">{{ item.title }}</h5>
            <small class="flex-grow-1">{{ $t('general.updated') }}: {{ item.modified_on | date }}</small>
            <div class="media-actions d-flex" v-if="item.file">
                <button @click="openDocument" class="btn btn-secondary btn-sm"><i class="fa fa-file-pdf"></i>
                    <span class="d-none d-md-inline">{{ $t('general.open') }}</span>
                </button>
                <a role="button" :href="`../${item.file}`" class="btn btn-secondary btn-sm" download><i class="fa fa-download"></i>
                    <span class="d-none d-md-inline">{{ $t('general.download') }}</span>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['item'],
    methods: {
        openDocument() {
            this.$pdfModal.show(this.item.title, `../${this.item.file}`);
        }
    }
}
</script>

<style lang="scss" scoped>
.media {
    height: 150px;
    min-width: 300px;
    border-radius: 3px;
    img {
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
}
</style>