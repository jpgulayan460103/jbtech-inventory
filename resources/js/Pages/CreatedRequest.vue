<template>
    <div class="container">

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reject Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectForm" @submit.prevent="submitRejectForm">
                        <div class="form-group">
                            <label for="prefix">Reason</label>
                            <input type="text" class="form-control" aria-describedby="emailHelp" v-model="rejectFormData.reject_remarks" placeholder="Enter reason for rejection" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="rejectForm" class="btn btn-primary">Submit</button>
                </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <table style="width: 100%;">
                    <tr>
                        <td>Request number: <b>{{clonedCreatedRequest.request_number}}</b>
                            <a v-if="clonedCreatedRequest.status == 'pending' && user.account_type != 'user' && user.warehouse_id ==  clonedCreatedRequest.warehouse_id" :href="`/requests/${clonedCreatedRequest.id}/process`" type="button" class="btn btn-primary btn-sm">Process</a>
                            <button v-if="clonedCreatedRequest.status == 'pending' && user.account_type != 'user' && user.warehouse_id ==  clonedCreatedRequest.warehouse_id" type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">Reject</button>
                        </td>
                        <td>
                            Reqest Type: <b>{{clonedCreatedRequest.request_type}}</b>
                        </td>
                    </tr>
                    <tr>
                        <td>Customer Name: <b>{{clonedCreatedRequest.customer_name}}</b></td>
                        <td>Requested On: <b>{{clonedCreatedRequest.created_at}}</b></td>
                    </tr>
                    <tr>
                        <td>Requested Warehouse: <b>{{clonedCreatedRequest.warehouse ? clonedCreatedRequest.warehouse.name : ""}}</b></td>
                        <td>Requested By: <b>{{clonedCreatedRequest.requester ? clonedCreatedRequest.requester.name : ""}}</b></td>
                    </tr>
                    <tr>
                        <td>Requester Remarks: <b>{{clonedCreatedRequest.remarks}}</b></td>
                        <td>
                            Status: <b>
                                <span  v-if="clonedCreatedRequest.status == 'processed' || clonedCreatedRequest.status == 'received'">Processed by {{ clonedCreatedRequest.processor.name }}</span>
                                <span  v-else-if="clonedCreatedRequest.status == 'rejected'">
                                    Rejected on {{ clonedCreatedRequest.updated_at }} <br>
                                    {{ clonedCreatedRequest.reject_remarks }}
                                </span>
                                <span  v-else>Pending</span>
                            </b>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <table class="table no-print">
                    <thead>
                        <tr>
                            <th scope="col">Item Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Stock</th>
                            <th scope="col" style="text-align:center">Qty</th>
                            <th scope="col" style="text-align:center" v-if="clonedCreatedRequest.status == 'processed'">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in clonedCreatedRequest.items" :key="index">
                            <td>{{ item.item ? item.item.name : "" }}</td>
                            <td>{{ item.item ? item.item.category : "" }}</td>
                            <td>{{ item.stock_month }}</td>
                            <td style="text-align:center">{{ item.requested_quantity }}</td>
                            <td style="text-align:center" v-if="clonedCreatedRequest.status == 'processed'">
                                <span v-if="item.is_rejected">Rejected</span>
                                <span v-else>Processed</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Item Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Serial Number</th>
                            <th scope="col" style="text-align:center">Qty</th>
                            <th scope="col" style="text-align:center">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in clonedCreatedRequest.serials" :key="index">
                            <td>{{ item.item ? item.item.name : "" }}</td>
                            <td>{{ item.item ? item.item.category : "" }}</td>
                            <td>{{ item.item_detail ? item.item_detail.serial_number : "" }}</td>
                            <td style="text-align:center">{{ item.quantity }}</td>
                            <td style="text-align:center;border-bottom:1px solid black;width:150px"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <br>
        <div class="row" v-if="clonedCreatedRequest.status == 'processed' || clonedCreatedRequest.status == 'received'">
            <div class="col-sm-12">
                <table style="width:100%">
                    <tr>
                        <td style="width: 50%">PREPARED BY</td>
                        <td style="width: 50%">RECEIVED BY</td>
                    </tr> 
                    <tr>
                        <td style="width: 50%;"><br>_____________________________________</td>
                        <td style="width: 50%;"><br>_____________________________________</td>
                    </tr>
                    <tr>
                        <td style="width: 50%">VALIDATED BY</td>
                        <td style="width: 50%">DELIEVERED BY</td>
                    </tr> 
                    <tr>
                        <td style="width: 50%;"><br>_____________________________________</td>
                        <td style="width: 50%;"><br>_____________________________________</td>
                    </tr>   
                </table>                
            </div>
        </div>
        <button class="btn btn-primary no-print" v-if="clonedCreatedRequest.status == 'processed' || clonedCreatedRequest.status == 'received'" @click="printRq">
            Print
        </button>
        <button class="btn btn-success no-print" v-if="clonedCreatedRequest.request_type == 'stock_transfer' && clonedCreatedRequest.status == 'processed' && clonedCreatedRequest.requester_id == user.id" @click="receiveItems">
            Receive Items
        </button>
    </div>
</template>

<script>
    import _cloneDeep from 'lodash/cloneDeep'
    import _debounce from 'lodash/debounce'
import Button from '../../../vendor/laravel/breeze/stubs/inertia-vue/resources/js/Components/Button.vue';
    export default {
  components: { Button },
        mounted() {
            this.clonedCreatedRequest = _cloneDeep(this.createdRequest);
        },
        props: ['createdRequest','user'],
        data() {
            return {
                clonedCreatedRequest: {},
                rejectFormData: {
                    reject_remarks: "",
                }
            }
        },
        methods: {
            printRq(){
                window.print()
            },
            receiveItems: _debounce(function() {
                axios.post(`/api/request/${this.createdRequest.id}/receive`, {
                    user_id: this.user.id,
                    warehouse_id: this.user.warehouse_id
                })
                .then(res => {
                    window.location.reload();
                })
                .catch(err => {})
                .then(res => {})
            }, 150),
            submitRejectForm: _debounce(function() {
                axios.put(`/api/request/${this.createdRequest.id}`, {
                    status: 'rejected',
                    reject_remarks: this.rejectFormData.reject_remarks,
                })
                .then(res => {

                })
                .catch(err => {})
                .then(res => {})
            }, 150)
        }
    }
</script>