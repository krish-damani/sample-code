    <template>
    <div class="row" v-if="!chartLoader">
        <div class="col-12 col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header font-weight-bold d-flex align-items-center justify-content-between">
                    <div class="card-header-title">Channel Performance - SEO</div>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <div v-if="noSeoRecords" class="text-center">
                            No data found
                        </div>
                        <template v-else>
                            <doughnut-chart :data="seoChart"></doughnut-chart>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header font-weight-bold d-flex align-items-center justify-content-between">
                    <div class="card-header-title">Channel Performance - Website</div>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <div v-if="noWebsiteRecords" class="text-center">
                            <h1>No data found</h1>
                        </div>
                        <template v-else>
                            <doughnut-chart :data="websiteChart"></doughnut-chart>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import {mapState} from "vuex";
    import DoughnutChart from "../DoughnutChart";

    export default {
        name: "DashboardPerformance",
        components: {
            DoughnutChart
        },
        created() {
            this.$store.dispatch('report/getCharts');
        },
        computed: mapState({
            chartLoader: state => state.report.chartLoader,
            noSeoRecords(state) {
                return Object.keys(state.report.chartReports.seoChart).every((k) => !state.report.chartReports.seoChart[k]);
            },
            noWebsiteRecords(state) {
                return Object.keys(state.report.chartReports.websiteChart).every((k) => !state.report.chartReports.websiteChart[k]);
            },
            seoChart(state) {
                return {
                    labels: ["Organic Direct", "Google Ads", "B2B"],
                    datasets: [
                        {
                            label: "Data One",
                            backgroundColor: ["#A29EF7", "#FF8276", "#40D7FB"],
                            data: Object.values(state.report.chartReports.seoChart)
                        }
                    ]
                }
            },
            websiteChart(state) {
                return {
                    labels: ["Klook", "Expedia", "TC"],
                    datasets: [
                        {
                            label: "Data One",
                            backgroundColor: ["#6c6", "#008cff", "#ff7a8a"],
                            data: Object.values(state.report.chartReports.websiteChart)
                        }
                    ]
                }
            },
        }),
    }
</script>
