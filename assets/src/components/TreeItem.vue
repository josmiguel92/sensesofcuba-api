<template>
    <li class="tree-item" @click.stop="toggle">
        <div id="tree-item-content" class="d-flex align-items-center p-2 m-2">
			<i class="mr-2 ml-2" :class="icon"></i>
            <span class="title flex-grow-1">{{ item.title }}</span>
            <div class="d-flex align-items-center" v-if="item.file">
                <button class="btn btn-secondary btn-sm mr-2" @click="showModal(item)"><i class="fa fa-file"></i> Open</button>
                <a class="btn btn-primary btn-sm" role="button" :href="`/sensesofcuba/${item.file}`" download><i class="fa fa-download"></i> Download</a>
            </div>
        </div>
        <ul v-if="isParent" v-show="isOpen" class="tree">
            <tree-item v-for="(child, index) in item.children" :key="index" :item="child"></tree-item>
        </ul>
    </li>
</template>

<script>
export default {
    name: 'tree-item',
    props: {
        item: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
			isOpen: false,
			modalOpen: false
        }
    },
    computed: {
        isParent() {
            return !!this.item.children;
		},
		icon() {
			if (!this.isOpen && this.isParent) return "fa fa-chevron-down";
			else if (this.isOpen && this.isParent) return "fa fa-chevron-up";
			else return "fa fa-tag";
		}
    },
    methods: {
        toggle() {
            if (this.isParent) {
                this.isOpen = !this.isOpen;
            }
        },
        showModal(item) {
            this.$modal.show(item.title, `/sensesofcuba/${item.file}`);
        }
    },
    updated() {
        this.$parent.$forceUpdate();
    }
}
</script>

<style lang="scss">
#tree-item-content {
	height: 46px;
	border-radius: 3px;
	background-color: #eee;
	.title {
		font-weight: 500;
	}
}
</style>