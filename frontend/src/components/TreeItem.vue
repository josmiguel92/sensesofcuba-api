<template>
    <div @click.stop="toggle" class="tree-item">
        <div class="content d-flex align-items-center">
            <div class="flex-grow-1 d-flex align-items-center">
                <span class="label d-flex align-items-center ml-2 mr-2"><i class="mr-2" :class="iconClass"></i> {{ item.title }}</span>
                <span v-if="!isParent" class="mr-2"> | </span>
                <small v-if="!isParent && item.modified_on" class="d-none d-md-inline">{{ $t('general.updated') }}: {{ item.modified_on | date }}</small>
            </div>
            <div class="d-flex" v-if="item.file">
                <button @click="openDocument(item.title, `${item.file}`)" class="btn btn-secondary btn-sm btn-icon"><i class="fa fa-file-pdf mr-1"></i> <span class="d-none d-md-inline">Open</span></button>
                <a role="button" :href="`${item.file}`" class="btn btn-secondary btn-sm" :download="item.title">
                    <i class="fa fa-download mr-1"></i> <span class="d-none d-md-inline">Download</span>
                </a>
            </div>
        </div>
        <div class="tree" v-if="isParent" v-show="isOpen">
            <TreeItem v-for="(child, i) in item.children" :key="i" :item="child"></TreeItem>
        </div>
    </div>
</template>

<script>
export default {
    name: 'TreeItem',
    props: ['item'],
    data() {
        return {
            isOpen: false
        }
    },
    computed: {
        isParent() {
            return !!this.item.children;
        },
        iconClass() {
            if (this.isParent) {
                if (this.isOpen) {
                    return 'fa fa-chevron-up';
                } else {
                    return 'fa fa-chevron-down';
                }
            } else {
                return 'fa fa-tag';
            }
        }
    },
    methods: {
        toggle() {
            if (this.isParent) {
                this.isOpen = !this.isOpen;
            }
        },
        openDocument(title, file) {
            this.$pdfModal.show(title, file, this.$t('general.close'));
        }
    },
    updated() {
        this.$parent.$forceUpdate();
    }
}
</script>

<style lang="scss" scoped>
.tree-item {
    margin-top: 4px;
    .content {
        background-color: #eee;
        border-radius: 3px;
        padding: 4px 8px;
        height: 46px;
        .label {
            font-weight: 500;
        }
    }
    .tree {
        margin-left: 16px;
    }
}

</style>