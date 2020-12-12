<template>
    <div class="doc-item p-2 bg-secondary d-flex flex-column col-md-5 col-sm-12 col-xl-5">
        <div class="d-flex ml-2 mr-2 flex-column flex-grow-1">
            <h3 class="mt-2">{{ item.title }}</h3>
            <span v-if="item.description">
                <i class="ml-2 fa fa-info-circle"></i> {{ item.description }}
            </span>
        </div>
        <div class="d-flex ml-2 mr-2 align-items-center">
            <small v-if="item.modified_on" class="flex-grow-1">{{ $t('general.updated') }}: {{ item.modified_on | date }}</small>
            <div class="d-flex" v-if="item.file">
                <OpenDocumentButton :item="item"></OpenDocumentButton>
                <a role="button" :href="`${item.file}`" class="btn btn-secondary btn-sm" :download="item.title"><i class="fa fa-download"></i>
                    <span class="d-none d-md-inline">{{ $t('general.download') }}</span>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import OpenDocumentButton from './OpenDocumentButton.vue';

export default {
    props: ['item'],
    components: {
        OpenDocumentButton
    }
}
</script>

<style scoped>
.doc-item {
    min-width: 300px;
    min-height: 150px;
}
</style>
