<template>
    <div class="modal-backdrop" v-show="isOpen" @click.stop>
        <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1">
            <div class="content card d-flex flex-column">
                <h4 class="card-title p-2"><i class="fa fa-tag"></i> {{ title }}</h4>
                <div class="card-content flex-grow-1">
                    <iframe id="view" :src="file"></iframe>
                </div>
                <div class="card-actions mt-2 d-flex align-items-center justify-content-end">
                    <button @click="close" class="btn btn-secondary">{{ closeText }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'PDFModal',
    data() {
        return {
            isOpen: false,
            file: undefined,
            title: undefined,
            closeText: ''
        }
    },
    methods: {
        show(title, file, closeText = 'Close') {
            this.isOpen = true;
            this.title = title;
            this.file = file;
            this.closeText = closeText;
        },
        close() {
            this.isOpen = false;
        }
    },
    watch: {
        isOpen: function(val) {
            let docClasses = document.body.classList;
            if (val) {
                docClasses.add('modal-open');
            } else {
                docClasses.remove('modal-open');
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.modal-backdrop {
    position: fixed;
    z-index: 99999;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    display: flex;
    background-color: rgba(0,0,0,0.3);
    .content {
        width: 90%;
        height: 96%;
        padding: 1em;
    }
    #view {
        width: 100%;
        height: 100%;
    }
}
</style>