<template>
    <div class="card card-flush shadow-sm mb-2">
        <div class="card-header">
            <h5 class="card-title"></h5>
            <div class="card-toolbar">
                <button type="button" title="Recharger" @click="reloadData"  class="btn btn-sm btn-light">
                    <i class="fas fa-sync-alt fs-6"></i> Réactualiser
                </button>
            </div>
        </div>
    </div>
    <div class="card card-flush shadow-sm">
            <!-- DOC here  https://vuejsexamples.com/a-easy-to-use-data-table-component-made-with-vue-js-3-x/ -->
            <easy-data-table class="table table-bordered" 
            header-text-direction="center"
            show-index
            :headers="headers"  :loading="loading" hide-footer border-cell :items="items"  alternating :theme-color="'#181C32'" @expand-row="loadMore">
            <template #header-jan="header">
                    {{  header.text }} 
            </template>
            <template #loading>
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading ...</span>
                    </div>
                </div>
            </template>
            <template #expand="item">
                    <template v-if="item.more" >
                        <div style="padding: 10px" class="shadow p-3 mb-5 bg-white rounded">
                            <div class="text-center"  v-html="messageInfo"></div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Ref</th>
                                        <th scope="col">Dossier</th>
                                        <th scope="col">Types de projet</th>
                                        <th scope="col">Version</th>
                                        <th scope="col">Montage</th>
                                        <th scope="col">Pôle</th>
                                        <th scope="col">Durées</th>
                                        <th scope="col">Point</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="loadingList">
                                        <td colspan="9"> 
                                                <div  class="d-flex justify-content-center">
                                                    <div class="spinner-border" role="status">
                                                        <span class="visually-hidden">Loading ...</span>
                                                    </div>
                                                </div>
                                            </td>  
                                        </tr>
                                    <template v-if="item.list && !loadingList">
                                        <template   v-for="(list, index) in item.list" :key="index">
                                            <tr>
                                                <th scope="row">{{ list.suivi.ref }}</th>
                                                <td>{{ list.suivi.folder_name }}</td>
                                                <td>{{ list.typesName }} </td>
                                                <td>{{ list.version.title }}</td>
                                                <td>{{ list.montage }}</td>
                                                <td>{{ list.poles }}</td>
                                                <td>{{ secondsToDhms(list.secondes) }}</td>
                                                <td> 
                                                    <div class="d-flex align-items-center">
                                                    <div class="fs--2 fw-bold counted" >{{ list.realPointItem }}</div>
                                                    </div>
                                                </td>
                                                <td> <span class="badge badge-light-success fs-7 fw-semibold">Terminer</span> </td>
                                            </tr>
                                        </template>
                                    </template>
                                    <template v-if="!loadingList && (item.list && !item.list.length )">
                                        <tr> 
                                            <td colspan="9"> 
                                                <div class="text-center"> Pas de donneés</div>
                                            </td>  
                                        </tr> 
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
            </template>
            <template #item-jan="{index, user_id , jan}">
                <div class="text-info" @click="loadDetailPoint(index , user_id, 1,jan ,'jan' )"> {{ jan }} </div>
            </template>
            <template #item-fev="{index, user_id , fev}">
                <div class="text-info" @click="loadDetailPoint(index , user_id,2,fev,'fev' )"> {{ fev }} </div>
            </template>
            <template #item-mars="{index, user_id , mars}">
                <div class="text-info" @click="loadDetailPoint(index , user_id,3, mars,'mars')"> {{ mars }} </div>
            </template>
            <template #item-avr="{index, user_id , avr}">
                <div class="text-info" @click="loadDetailPoint(index , user_id,4, avr,'avr')"> {{ avr }} </div>
            </template>
            <template #item-mai="{index, user_id , mai}">
                <div class="text-info" @click="loadDetailPoint(index , user_id,5, mai,'mai')"> {{ mai }} </div>
            </template>
            <template #item-juin="{index, user_id , juin}">
                <div class="text-info" @click="loadDetailPoint(index , user_id,6, juin,'juin')"> {{ juin }} </div>
            </template>
            <template #item-juil="{index, user_id , juil}">
                <div class="text-info" @click="loadDetailPoint(index , user_id,7, juil,'juil')"> {{ juil }} </div>
            </template>
            <template #item-aout="{index, user_id , aout}">
                <div class="text-info" @click="loadDetailPoint(index , user_id,8,aout,'aout')"> {{ aout }} </div>
            </template>
            <template #item-sept="{index, user_id , sept}">
                <div class="text-info" @click="loadDetailPoint(index , user_id,9, sept,'sept')"> {{ sept }} </div>
            </template>
            <template #item-oct="{index, user_id , oct}">
                <div class="text-info" @click="loadDetailPoint(index , user_id,10, oct,'oct')"> {{ oct }} </div>
            </template>
            <template #item-nov="{index, user_id , nov}">
                <div class="text-info" @click="loadDetailPoint(index , user_id,11, nov,' nov')"> {{ nov }} </div>
            </template>
            <template #item-dec="{index, user_id , dec}">
                <div class="text-info" @click="loadDetailPoint(index , user_id, 12, dec,' dec')"> {{ dec }} </div>
            </template>
           
        </easy-data-table>
    </div>
</template>
<style  >
    span.header.direction-center {
    position: inherit;
    top: 0;
    background-color: white;
    height: auto;
    box-shadow: 0px 10px 30px 0px rgba(82, 63, 105, 0.05);
}
</style>
<script>
import axios from 'axios';
export default {
    data() {
        return {
            app : app,
            dataList  : [],
            months : [],
            loading : false,
            loadingList : false,
            messageInfo: "",
            headers: [
                { text: "N°", value: "mac" , fixed: true, width: 50 },
                { text: "Nom & prénom", value: "user" , fixed: true, width: 200 },
                { text: "Janvier", value: "jan" , width: 70},
                { text: "Février", value: "fev" , width: 70},
                { text: "Mars", value: "mars" , width: 70},
                { text: "Avril", value: "avr" , width: 70},
                { text: "Mai ", value: "mai", width: 70},
                { text: "Juin", value: "juin", width: 70},
                { text: "Juillet", value: "juil", width: 70},
                { text: "Août", value: "aout", width: 70},
                { text: "Septembre", value: "sept", width: 70},
                { text: "Octobre", value: "oct", width: 70},
                { text: "Novembre", value: "nov", width: 70},
                { text: "Décembre", value: "dec", width: 70},
            ],
            items: [],
        }
    },
    mounted() {
        this.loadData()
    },
    methods: {
       async loadMore(index , message = ""){
            let expandedItem  = this.items[index];
            this.items[index].list = [];
            if (!expandedItem.more) {
                expandedItem.expandLoading = true;
                 expandedItem.more = await this.loadMoreDataItem(message);
                setTimeout(() => {
                    expandedItem.expandLoading = false;
                }, 2000);
            }
        },
        async loadMoreDataItem(message){
            return message ? message : "Cliquer un point ";
        },
        loadData(){
            this.loading = true;
            axios.post(this.app.baseUrl + "/suivi/data/recap", {"test" : "all"}).then(response => {
                if (response.data.success) {
                    let result = response.data.result
                    this.months = response.data.months
                    this.items = this.makeDataForTable(result)
                }
            })
        },
        reloadData(){
            this.items = [];
            return this.loadData();
        },
        makeDataForTable(data = []){
            let list = [];
            data.forEach((item) => {
                list.push({
                    "user_id"  : item.id,
                    "mac"  : item.registration_number,
                    "user"  : item.fullName,
                    "jan" : item.traitement_grouped.jan.total_point_traitement ,
                    "fev" : item.traitement_grouped.fev.total_point_traitement,
                    "mars" : item.traitement_grouped.mars.total_point_traitement,
                    "avr" : item.traitement_grouped.avr.total_point_traitement,
                    "mai" : item.traitement_grouped.mai.total_point_traitement,
                    "juin" : item.traitement_grouped.juin.total_point_traitement,
                    "juil" : item.traitement_grouped.juil.total_point_traitement,
                    "aout" : item.traitement_grouped.aout.total_point_traitement,
                    "sept" : item.traitement_grouped.sept.total_point_traitement,
                    "oct" : item.traitement_grouped.oct.total_point_traitement,
                    "sept" : item.traitement_grouped.sept.total_point_traitement,
                    "nov" : item.traitement_grouped.nov.total_point_traitement,
                    "dec" : item.traitement_grouped.dec.total_point_traitement,
                })
            });
            this.loading = false;
            return list;
        },
        async loadDetailPoint(index , user_id, month , point_value , month_text){
            let real_index = index - 1 ;
            let all_expand_icon = $(".expand-icon");
            let curent_expand_icon = all_expand_icon[real_index];
            if (curent_expand_icon) {
                if (!curent_expand_icon.classList.contains("expanding")) {
                    all_expand_icon[real_index].click()
                    for (let i = 0; i < all_expand_icon.length; i++) {
                        if (real_index != i) {
                            let other_icon = all_expand_icon[i]
                            if (other_icon.classList.contains("expanding")) {
                                other_icon.classList.remove("expanding");
                                other_icon.click();
                            }
                        }
                    }
                }
            }
           this.loadingList = true;
           this.items[real_index].more = "Historique du point mois de " +  month_text  + " : " + '<span class="text-info">' +point_value +' </span>';
           this.messageInfo =  "Historique du point mois de " +  month_text  + " : " + '<span class="text-info">' +point_value +' </span>';
           
           // this.items[real_index].list = [] ;
           
           let expandedItem  = this.items[real_index];
            if (!expandedItem.list) {
                expandedItem.expandLoading = true;
            }
           await axios.post(this.app.baseUrl + "/suivi/data/recap/point-list", {"month" : month , "user_id" : user_id}).then(response => {
                if (response.data.success) {
                    let result = response.data.result
                    this.items[index - 1].list = result;
                }
                this.loadingList = false;
                expandedItem.expandLoading = false;
            })
        },
        secondsToDhms(seconds) {
            return secondsToDhms(seconds);// view/includes/helper-js
       }
    },
}
</script>