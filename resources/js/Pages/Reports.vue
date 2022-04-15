<template>
    <div class="container">
        <div class="row" v-if="user.account_type == 'admin'">
            <div class="col-md-2">
                Select Warehouse
                <select class="form-control form-control-sm" v-model="warehouse_id" @change="changeWarehouse">
                    <option value="">All Warehouse</option>
                    <option :value="warehouse.id" v-for="warehouse in warehouses" :key="warehouse.id">{{ warehouse.name }}</option>
                </select>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col" rowspan="2">Category</th>
                    <th scope="col" rowspan="2">Item Name</th>
                    <th scope="col" colspan="3" style="text-align:center">{{ date }}</th>
                </tr>
                <tr>
                    <th scope="col" style="text-align:center">Starting</th>
                    <th scope="col" style="text-align:center">In</th>
                    <th scope="col" style="text-align:center">Transferred</th>
                    <th scope="col" style="text-align:center">Out</th>
                    <th scope="col" style="text-align:center">Deleted</th>
                    <th scope="col" style="text-align:center">Remaining</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in reportData" :key="item.id">
                    <td>{{ item.category }}</td>
                    <td>{{ item.name }}</td>
                    <td style="text-align:center">{{ item.previous_remaining }}</td>
                    <td style="text-align:center">{{ item.total_in }}</td>
                    <td style="text-align:center">{{ item.total_stock_transfer }}</td>
                    <td style="text-align:center">{{ item.total_out }}</td>
                    <td style="text-align:center">{{ item.total_deleted }}</td>
                    <td style="text-align:center">{{ item.total_remaining }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.warehouse_id = this.warehouseId;
        },
        props: ['reportData','user','warehouses', 'warehouseId', 'date'],
        data() {
            return {
                warehouse_id: ""
            }
        },
        methods: {
            changeWarehouse(){
                window.location = `/reports?warehouse=${this.warehouse_id}`
            }
        },
    }
</script>