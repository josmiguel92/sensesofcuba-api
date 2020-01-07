<template>
    <div class="modal fade" :class="[{'show d-block': show}, {'d-none': !show}]" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Options</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <label for="">Change your user credentials</label>
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-envelope"></i>
                            </span>
                        </div>
                        <input type="email" class="form-control" placeholder="Email" v-model="email">
                    </div>
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-lock"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" placeholder="Password">
                    </div>
                    <div class="form-group input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-lock"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" placeholder="Repeat Password">
                    </div>
                </div>
                <div>
                    <label for="">Change company details</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="close">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
    name: 'UserModal',
    props: ['show'],
    computed: {
        ...mapGetters(['currentUser'])
    },
    methods: {
        close() {
            this.$emit('close');
        }
    },
    data() {
        return {
            email: '',
        }
    },
    mounted() {
        this.email = this.currentUser.currentUser;
    },
    watch: {
        show(val) {
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

<style>

</style>