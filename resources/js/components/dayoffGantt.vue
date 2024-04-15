<template v-if="notLoading">
    <g-gantt-chart
        precision="day"
        :chart-start="chartConf.chart_start_date"
        :chart-end="chartConf.chart_end_date"
        bar-start="startDateDayOff"
        bar-end="returnDateDayOff"
        row-height ="40"
       
        grid color-scheme="dark" no-overlap >
        <!-- <template #bar-tooltip = "{bar}" >
                    <span>{{ gooo(bar) }} xxxxxx</span> 
        </template> -->
        <template v-for="(item , index) in usersOnDayOff "  :key="index" >
            <g-gantt-row  :bars="item">
                <template #label>
                    <div class="symbol symbol-30px symbol-circle"  data-kt-initialized="1">
                        <img alt="photo" :src='getLabelImage(item)'>
                    </div>
                    &nbsp; {{ getLabel(item) }}
                </template>
                <template #bar-label="{bar}">
                    <!-- <img v-if="bar.imgSrcRow" :src="bar.imgSrcRow" height="30" width="30"/> -->
                    <!-- <div style="color: white; width: calc(100% + 20px); display: flex; justify-content: space-between; align-items: center; margin: 0 -10px;"> -->
                    <div style="color: white; justify-content:left" :title="bar.ganttBarConfig.label">
                        <!-- <i  style="color:white" class="fas fa-angle-double-left fs-1"></i>  -->
                            {{bar.ganttBarConfig.label}} 
                        <!-- <i style="color:white" class="fas fa-angle-double-right fs-1"></i>   -->
                    </div>
                </template>
            </g-gantt-row>
        </template>
    </g-gantt-chart>
</template>
<script>
import axios from 'axios';
/** 
 * The doc ganttastic is here : https://zunnzunn.github.io/vue-ganttastic/introduction.html 
 * example  : https://github.com/zunnzunn/vue-ganttastic
 */
import dayjs from 'dayjs';
import 'dayjs/locale/fr';
dayjs.locale('fr');
export default {
    data() {
        return {
            app: app,
            notLoading : false ,
            usersOnDayOff : [],
            chartConf : {},
        }
    },
    mounted (){
        this.getUsersOnDayOffAccepted();
    },
    methods : {
       
        getUsersOnDayOffAccepted(){
            this.notLoading =   false ,
            axios.get(this.app.baseUrl + "/days-off/dataListGantt").then(response => {
                if (response.data.success) {
                    console.log( response.data);
					this.usersOnDayOff = response.data.response;
					this.chartConf = response.data.chart_conf;
                }
                this.notLoading =  true;
                this.reformatWidth();
            })
        },
        reformatWidth() {
            setTimeout(() => {
                document.getElementsByClassName('g-gantt-rows-container')[0].style.width = document.getElementsByClassName('g-timeaxis')[0].clientWidth+"px";
            }, 300);
        },
        getLabel(item = []){
            return item[0].user.sortName;
        },
        getLabelImage(item = []){
            return item[0].user.avatarUrl;
        },
        gooo(item ){
            console.log( item);
            return "5445 "
        }  
    }
    
}
</script>
<style >
.g-gantt-bar-label {
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    padding: 0 14px 0 14px;
    display: flex;
    justify-content: left;
    align-items: center;
}
.g-gantt-row-label {
    background: #181C32 !important;
    color: aliceblue  !important;
    position: absolute;
    top: 0;
    left: 0px;
    padding: 0px 8px;
    display: flex;
    align-items: center;
    height: 100%;
    min-height: 20px;
    font-size: 1.1em;
    font-weight: bold;
    border-bottom-right-radius: 0px;
    z-index: 3;
    box-shadow: 0px 1px 4px 0px rgba(0, 0, 0, 0.6);
}
.g-gantt-chart {
    padding-left: 250px;
    /* overflow-x: scroll; */
}
.g-gantt-row-label {
    width: 250px;
    left: -250px;
}
/* .g-timeaxis {
    min-width: max-content;
} */
/* .g-timeunits-container {
    min-width: max-content;
} */
.g-timeunit {
    font-size: 12px;
}
.g-grid-container {
    padding-left: 250px;
}
/* .g-grid-container .g-grid-line {
    padding: 0px 15px;
    width: 100px!important;
    min-width: 100px!important;
} */

</style>