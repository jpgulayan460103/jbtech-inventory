<template>
    <div class="container">
        <p>Request number: <b>{{clonedCreatedRequest.request_number}}</b> <a v-if="clonedCreatedRequest.status != 'processed'" :href="`/requests/${clonedCreatedRequest.id}/process`" type="button" class="btn btn-primary btn-sm">Process</a></p>
        <p>Customer Name: <b>{{clonedCreatedRequest.customer_name}}</b></p>
        <p>Requested Warehouse: <b>{{clonedCreatedRequest.warehouse ? clonedCreatedRequest.warehouse.name : ""}}</b></p>
        <p>Remarks: <b>{{clonedCreatedRequest.remarks}}</b></p>
        <p>Requested On: <b>{{clonedCreatedRequest.created_at}}</b></p>
        <p>Requested By: <b>{{clonedCreatedRequest.requester.name}}</b></p>
        <p>Status: <b>
            <span  v-if="clonedCreatedRequest.status == 'processed'">Processed by {{ clonedCreatedRequest.processor.name }}</span>
            <span  v-else>Pending</span>
        </b></p>

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Item Name</th>
                    <th scope="col">Category</th>
                    <th scope="col">Quantity</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in clonedCreatedRequest.items" :key="index">
                    <td>{{ item.item ? item.item.name : "" }}</td>
                    <td>{{ item.item ? item.item.category : "" }}</td>
                    <td>{{ item.requested_quantity }}</td>
                </tr>
            </tbody>
        </table>
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

        }
    }
</script>