<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                Select Warehouse
                <select class="form-control form-control-sm" v-model="warehouse_id" @change="changeWarehouse">
                    <option value="">All Warehouse</option>
                    <option :value="warehouse.id" v-for="warehouse in warehouses" :key="warehouse.id">{{ warehouse.name }}</option>
                </select>
            </div>
            <div class="col-md-2">
                Select Month
                <input type="month" v-model="date"  class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                &nbsp;<br>
                <button class="btn btn-primary btn-sm" @click="getReports">Add Report</button>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="table-responsive col-md-12">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th scope="col" rowspan="2">Category</th>
                            <th scope="col" rowspan="2">Item Name</th>
                            <th scope="col" colspan="6" style="text-align:center" v-for="(report, index) in reports" :key="index" class="borderEnd borderStart">
                                <span>{{ report.date }}</span>
                                <div class="float-end">
                                    <button class="btn btn-sm btn-danger" @click="removeReport(report.uuid)">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                                <div class="float-end">
                                    <button class="btn btn-sm btn-success" @click="generateExcel(report)">
                                        <i class="bi bi-file-earmark-arrow-down"></i>
                                    </button>
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th scope="col" style="text-align:center"  v-for="(reportQuantity, index) in reportQuantities" :key="index"  :class="borderClass(reportQuantity)">{{ reportQuantity.label }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in items" :key="item.id">
                            <td>{{ item.category }}</td>
                            <td>{{ item.name }}</td>
                            <td style="text-align:center" v-for="(reportQuantity, index) in reportQuantities" :key="index" :class="borderClass(reportQuantity)">
                                {{ extractData(reportQuantity.uuid, item.id)[reportQuantity.variable] }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<style scoped>
.borderEnd{
    border-right: 2px solid black;
}
.borderStart{
    border-left: 2px solid black;
}
</style>

<script>
    import isEmpty from 'lodash/isEmpty'
    export default {
        mounted() {
            // this.getReports();
        },
        props: ['warehouses','items','user'],
        data() {
            return {
                warehouse_id: "",
                reports: [],
                reportQuantities: [],
                date: null,
            }
        },
        methods: {
            getReports(){
                axios.get('/reports/generate', {
                    params: {
                        date: this.date,
                        warehouse_id: this.warehouse_id,
                    }
                })
                .then(res => {
                    let result = res.data;
                    let index = this.reports.length;
                    result.index = index;
                    this.reports.push(result);
                    this.reportQuantities.push({
                        uuid: result.uuid,
                        label: "Starting",
                        variable: "previous_remaining"
                    });
                    this.reportQuantities.push({
                        uuid: result.uuid,
                        label: "In",
                        variable: "total_in"
                    });
                    this.reportQuantities.push({
                        uuid: result.uuid,
                        label: "Transferred",
                        variable: "total_stock_transfer"
                    });
                    this.reportQuantities.push({
                        uuid: result.uuid,
                        label: "Out",
                        variable: "total_out"
                    });
                    this.reportQuantities.push({
                        uuid: result.uuid,
                        label: "Deleted",
                        variable: "total_deleted"
                    });
                    this.reportQuantities.push({
                        uuid: result.uuid,
                        label: "Remaining",
                        variable: "total_remaining"
                    });
                })
                .catch(err => {})
                .then(res => {})
            },
            extractData(uuid, item_id){
                let report = this.reports.filter(i => i.uuid == uuid);
                report = report[0];
                if(isEmpty(report.items)){
                    return {
                        previous_remaining: 0,
                        total_in: 0,
                        total_stock_transfer: 0,
                        total_out: 0,
                        total_deleted: 0,
                        total_remaining: 0,
                    }
                }
                let item = report.items.filter(i => i.id == item_id);
                return item[0];
            },
            borderClass(quantity){
                if(quantity.variable == "previous_remaining"){
                    return "borderStart";
                }
                if(quantity.variable == "total_remaining"){
                    return "borderEnd";
                }
            },
            removeReport(uuid){
                this.reports = this.reports.filter(i => i.uuid != uuid);
                this.reportQuantities = this.reportQuantities.filter(i => i.uuid != uuid);
            },
            changeWarehouse(uuid){
                this.reports = this.reports.filter(i => i.uuid != uuid);
                this.reportQuantities = this.reportQuantities.filter(i => i.uuid != uuid);
            },
            generateExcel({warehouse_id, date, uuid}){
                axios.post('/reports/generate/excel', {warehouse_id, date})
                .then(res => {
                    window.location = res.data.path;
                })
                ;
            }
        },
    }
</script>