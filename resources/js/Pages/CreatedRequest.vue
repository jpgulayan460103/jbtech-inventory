<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <table style="width: 100%;">
                    <tr>
                        <td>Request number: <b>{{clonedCreatedRequest.request_number}}</b> <a v-if="clonedCreatedRequest.status != 'processed'" :href="`/requests/${clonedCreatedRequest.id}/process`" type="button" class="btn btn-primary btn-sm">Process</a></td>
                        <td></td>
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
                        <td>Remarks: <b>{{clonedCreatedRequest.remarks}}</b></td>
                        <td>
                            Status: <b>
                                <span  v-if="clonedCreatedRequest.status == 'processed'">Processed by {{ clonedCreatedRequest.processor.name }}</span>
                                <span  v-else>Pending</span>
                            </b>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Item Name</th>
                            <th scope="col">Category</th>
                            <th scope="col" v-if="clonedCreatedRequest.status == 'processed'" style="text-align:center">Requested Qty</th>
                            <th scope="col" v-if="clonedCreatedRequest.status == 'processed'" style="text-align:center">Fulfilled Qty</th>
                            <th scope="col" v-else style="text-align:center">Qty</th>
                            <th scope="col" v-if="clonedCreatedRequest.status == 'processed'" style="text-align:center">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in clonedCreatedRequest.items" :key="index">
                            <td>{{ item.item ? item.item.name : "" }}</td>
                            <td>{{ item.item ? item.item.category : "" }}</td>
                            <td v-if="clonedCreatedRequest.status == 'processed'" style="text-align:center">{{ item.requested_quantity }}</td>
                            <td v-if="clonedCreatedRequest.status == 'processed'" style="text-align:center">{{ item.fulfilled_quantity }}</td>
                            <td v-else style="text-align:center">{{ item.requested_quantity }}</td>
                            <td v-if="clonedCreatedRequest.status == 'processed'" style="text-align:center;border-bottom:1px solid black;width:200px"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <br>
        <div class="row" v-if="clonedCreatedRequest.status == 'processed'">
            <div class="col-sm-12">
                <table style="width:100%">
                    <tr>
                        <td style="width: 50%">&nbsp;</td>
                        <td style="width: 50%">RECEIVED BY</td>
                    </tr> 
                    <tr>
                        <td style="width: 50%;"><br>&nbsp;</td>
                        <td style="width: 50%;"><br>_____________________________________</td>
                    </tr>   
                </table>                
            </div>
        </div>
        <button class="btn btn-primary no-print" v-if="clonedCreatedRequest.status == 'processed'" @click="printRq">
            Print
        </button>
    </div>
</template>

<script>
    import _cloneDeep from 'lodash/cloneDeep'
    export default {
        mounted() {
            this.clonedCreatedRequest = _cloneDeep(this.createdRequest);
        },
        props: ['createdRequest'],
        data() {
            return {
                clonedCreatedRequest: {}
            }
        },
        methods: {
            printRq(){
                window.print()
            }
        }
    }
</script>