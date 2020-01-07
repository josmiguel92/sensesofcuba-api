<template>
    <div class="grid">
        <div class="item"
        ref="items"
        v-for="(item, index) in tree"
        :key="index"
        @click="toggle(index)">
            <div class="item-content" :class="{ 'selected': currentItem === index }"
				v-lazy:background-image="`/sensesofcuba/${item.image}`">
                <span>{{ item.title }}</span>
            </div>
			<div class="item-panel" @click.stop v-show="currentItem === index" ref="panels">
				<div class="d-flex mb-2 ml-2">
					<h3 class="title">{{ item.title }}</h3>
					<b-button class="ml-4" type="primary" icon="fa fa-download" :disabled="!item.children">Download All</b-button>
				</div>
				<ul class="tree">
					<small class="tree-empty" v-if="!item.children"><i class="fa fa-info-circle"></i> No offers here yet</small>
					<tree-item v-for="(child, index) in item.children" :key="index" :item="child" v-else></tree-item>
				</ul>
            </div>
        </div>
    </div>
</template>

<script>
import TreeItem from '~/components/TreeItem.vue';

export default {
    name: 'product-tree',
    components: {
		TreeItem
    },
    props: {
        tree: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            currentItem: -1
        }
    },
    computed: {
        
    },
    methods: {
        toggle(index) {
            if (this.currentItem === index) {
                this.currentItem = -1;
            } else {
                this.currentItem = index;
            }
        }
    },
    updated() {
        this.$nextTick().then(() => {
            if (this.currentItem !== -1) {
                const item = this.$refs.items[this.currentItem];
                const panel = this.$refs.panels[this.currentItem];
                const margin = `${panel.clientHeight + 4}px`;
                item.style.marginBottom = margin;
                this.$refs.items
                .filter(i => i !== item)
                .forEach(i => (i.style.marginBottom = "0px"));
            } else {
                this.$refs.items.forEach(item => (item.style.marginBottom = '0px'));
            }
        });
    }
}
</script>

<style lang="scss">
.grid {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: flex-start;
    position: relative;
    width: 100%;
}
.item {
    flex: 1 0 auto;
    height: 160px;
    width: 260px;
    background: f9ce1c;
    margin: 2px;
    border-radius: 3px;
}
.item-content {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    position: relative;
    cursor: pointer;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: 1;
    &.selected {
        span {
            color: #f9ce1c;
        }
    }
    span {
        color: #fafafa;
        z-index: 10;
        font-weight: 700;
        z-index: 3;
        width: 100%;
        text-align: center;
        padding: 4px;
        background-color: rgba(0,0,0,.4);
        transition: all .3s ease;
    }
    &::before {
        position: absolute;
        content: " ";
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba($color: #000000, $alpha: 0.2);
        z-index: 2;
    }
}
.item-panel {
    position: absolute;
    top: auto;
    left: 1px;
    right: 1px;
    background: #fafafa;
    min-height: 200px;
    margin-top: 3px;
    display: flex;
    flex-direction: column;
    padding: 24px 32px;
    border-radius: 1px;
    .tree {
        list-style: none;
        padding: 0;
        flex-grow: 1;
        position: relative;
        .tree-empty {
            position: absolute;
            width: 100%;
			height: 100%;
            text-align: center;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            justify-content: center;
            color: lighten(#212121, 5);
            i {
                margin-right: 8px;
            }
        }
        .tree-item {
            cursor: pointer;
            .tree-item {
                margin-left: 16px;
            }
        }
        .tree-item .content {
            background: #eee;
            padding: 4px 8px;
            height: 42px;
            margin: 8px 0;
            border-radius: 3px;
            display: flex;
            align-items: center;
            i {
                color: currentColor;
                margin-right: 12px;
                margin-left: 8px;
            }
            span {
                font-weight: 500;
            }
            .actions {
                flex-grow: 1;
                display: flex;
                align-items: center;
                justify-content: flex-end;
            }
        }
    }
}
</style>