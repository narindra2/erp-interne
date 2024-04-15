<template>
    <div class="card card-flush shadow-sm mb-1 mt-1">
        <div class="card-header" style="min-height: 46px;">
            <h5 class="card-title">  </h5>
            <div class="card-toolbar">
            <div class="d-flex flex-stack flex-wrap gap-4">
                <span class="card-title fs-5">Colones :</span>
                <div class="d-flex align-items-center fw-bold">
                    <input id="versions" :value="versionsFilter" title= 'Versions'  placeholder="Versions ..."  @change="onChangeVersion($event.target.value)" class="form-select form-control-solid text-dark fs-7 lh-1 fw-bold py-0 ps-3 w-auto ">
                </div>
                |
                <div class="d-flex align-items-center fw-bold">
                    <input id="montages" :value="montagesFilter"  title= 'Montages' placeholder="Montages ..." @change="onChangeMontage($event.target.value)" class="form-select  form-control-solid text-dark fs-7 lh-1 fw-bold py-0 ps-3 w-auto ">
                </div>
                <span class="card-title fs-5">Sessions :</span>
                <div class="d-flex align-items-center fw-bold">
                    <input id="month" :value="monthFilter" title= 'Mois' placeholder="Mois ..."  @change="onChangeMonth($event.target.value)" class=" text-dark py-0 ps-3 w-auto ">
                </div>
                <div class="d-flex align-items-center fw-bold">
                    <input id="year" :value="yearFilter" title= 'Année' placeholder="Année ..."  @change="onChangeYear($event.target.value)" class="text-dark  py-0 ps-3 w-auto ">
                </div>
                <button type="button" title="Recharger" @click="reloadData"  class="btn btn-sm btn-light">
                    <i class="fas fa-sync-alt fs-6"></i> 
                </button>
            </div>
        </div>
            
        </div>
    </div>
    <div class="card card-flush shadow-sm">
            <!-- DOC here  https://vuejsexamples.com/a-easy-to-use-data-table-component-made-with-vue-js-3-x/ -->
            <easy-data-table class="table table-bordered" 
                header-text-direction="center"
                :headers="headers"  :loading="loading" hide-footer border-cell :items="items"  alternating :theme-color="'#181C32'" @expand-row="loadMore">
                <template #loading>
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading ...</span>
                        </div>
                    </div>
                </template>
                <template #header-seuil_point = "header">
                    <div class="customize-header">
                        {{ header.text }}  <i class="fas fa-edit " style="font-size:12px"></i>
                    </div>
                </template>
                <template #header-hours_works = "header">
                    <div class="customize-header">
                        {{ header.text }}  <i class="fas fa-edit " style="font-size:12px"></i>
                    </div>
                </template>
                <template #item-user="{user}">
                    <div v-html="user" > </div>
                </template>
                <template #item-days_work_nb="{days_work_nb}">
                    <div v-html="days_work_nb" > </div>
                </template>
                <template #item-seuil_point="{user_id,seuil_point}">
                    <input type="text" class="form-control form-control-solid"  @change=" updateSeuil (user_id ,$event.target.value)" placeholder="ex: 8.00" :value="seuil_point">
                </template>
                <template #item-hours_works="{user_id,hours_works}">
                    <input type="text" class="form-control form-control-solid" @change="updateHourWork(user_id, $event.target.value)" placeholder="ex: 3.39" :value="hours_works" >
                </template>
                <template #item-days_work_nb_dessi="{user_id,days_work_nb_dessi}">
                    <input type="text" class="form-control form-control-solid"  @change="updateDayworkDessi(user_id ,$event.target.value)" placeholder="1" :value="days_work_nb_dessi" >
                </template>
            </easy-data-table>
    </div>
</template>


<style>
    span.header.direction-center {
    position: inherit;
    top: 0;
    background-color: white;
    height: auto;
    box-shadow: 0px 10px 30px 0px rgba(82, 63, 105, 0.05);
}
.select2-container {
    box-sizing: border-box;
    display: inline-block;
    margin: 0;
    position: relative;
    vertical-align: middle;
    width: 144.1944px;
}
</style>
<script>
function tagTemplate(tagData){
    return `
        <tag title="${tagData.text}"
                contenteditable='false'
                spellcheck='false'
                tabIndex="-1"
                class="tagify__tag ${tagData.class ? tagData.class : ""}"  >
            <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
            <div>
                <span class='tagify__tag-text'>${tagData.text}</span>
            </div>
        </tag>
    `
}

function suggestionItemTemplate(tagData){
    return `
        <div ${this.getAttributes(tagData)}
            class='tagify__dropdown__item ${tagData.class ? tagData.class : ""}'
            tabindex="0"
            role="option">
            <strong>${tagData.text}</strong>
        </div>
    `
}
// import Tagify from '@yaireo/tagify'
// import '@yaireo/tagify/dist/tagify.css'
export default {
    props : ["versions" , "montages","months" , "years"],
    data() {
        return {
            app : app,
            dataList  : [],
            headers: [],
            items: [],
            loading: true,

            versionsFilter: null,
            montagesFilter: null,
            monthFilter: JSON.parse(this.months).find((month) => month.selected == true).value,
            yearFilter  : JSON.parse(this.years)[0].value,

            versionInput : null,
            montageInput : null,
            monthInput : null,
            yearInput : null,

            postData : {},
            settings :  {
                maxItems: 999,// list of item listing in dropdown
                classname: 'users-list',
                enabled       : 0,             
                position      : "text",         
                closeOnSelect : true,        
                highlightFirst: true
            },
        }
    },

    mounted() {
        this.versionInput = new Tagify(document.querySelector('#versions'),{
            whitelist :  JSON.parse(this.versions),
            tagTextProp: 'text',
            placeholder : "Version ...",
            enforceWhitelist : true,
            keepInvalidTags: false,
            skipInvalid: true,
            templates: {
                tag: tagTemplate,
                dropdownItem: suggestionItemTemplate,
            },
            dropdown : this.settings,
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(', ')
        });
        this.montageInput = new Tagify(document.querySelector('#montages'),{
            whitelist :   JSON.parse(this.montages),
            tagTextProp: 'text',
            placeholder : "Montage ...",
            enforceWhitelist : true,
            keepInvalidTags: false,
            skipInvalid: true,
            templates: {
                tag: tagTemplate,
                dropdownItem: suggestionItemTemplate,
            },
            dropdown : this.settings,
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(', ')
        });
        
        this.monthInput = new Tagify(document.querySelector('#month'),{
            enforceWhitelist : true,
            whitelist :  JSON.parse(this.months),
            tagTextProp: 'text',
            placeholder : "Mois ...",
            mode : "select",
            keepInvalidTags: false,
            skipInvalid: true,
            templates: {
                tag: tagTemplate,
                dropdownItem: suggestionItemTemplate,
            },
            dropdown : this.settings,
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(', ')
        });
        this.yearInput = new Tagify(document.querySelector('#year'),{
            enforceWhitelist : true,
            delimiters : null,
            whitelist :  JSON.parse(this.years),
            tagTextProp: 'text',
            mode : "select",
            placeholder : "Année ...",
            keepInvalidTags: false,
            skipInvalid: true,
            dropdown : this.settings,
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(', ')
        });
        this.postData.month =  this.monthFilter ;
        this.postData.year = this.yearFilter ;
        this.loadData();
    },
    
    methods: {
        loadData(){
            this.loading = true;
            axios.post(this.app.baseUrl + "/load/prod" , this.postData).then(response => {
                if (response.data.success) {
                    this.loading = false;
                    this.headers = response.data.headers
                    this.items = response.data.items
                }
            })
        },
        reloadData(){
            this.items = [];
            this.loadData();
        },
      
        updateHourWork(user_id , hours ){
            axios.post(this.app.baseUrl + "/suivi/save/hours_days_work",{ "user_id" : user_id, "month" : this.monthFilter , "year" : this.yearFilter , "hours_works" : hours }).then(response => {
                if (response.data.success) {
                  
                }
            })
        },
        updateSeuil(user_id , point ){
            axios.post(this.app.baseUrl + "/suivi/save/hours_days_work",{"user_id" : user_id, "month" : this.monthFilter , "year" : this.yearFilter , "seuil_point" : point }).then(response => {
                if (response.data.success) {
                    
                }
            })
        },
        updateDayworkDessi(user_id , days_work ){
            axios.post(this.app.baseUrl + "/suivi/save/hours_days_work",{"user_id" : user_id, "month" : this.monthFilter , "year" : this.yearFilter , "days_work" : days_work }).then(response => {
                if (response.data.success) {
                    
                }
            })
        },
        onChangeVersion (value){
            if (value) {
                this.versionsFilter = value;
                let array = value.split(",")
                this.postData.version_ids = array;
            }
        },
        onChangeMontage (value){
            if (value) {
                this.montagesFilter = value;
                console.log(array);
                let array = value.split(",")
                this.postData.montage_ids = array;
            }
        },
        onChangeMonth (value){
            if (value) {
                this.monthFilter = value;
                this.postData.month =  this.monthFilter ;
            }else{
                this.monthFilter =JSON.parse(this.months).find((month) => month.selected == true).value
            }
        },
        onChangeYear (value){
            if (value) {
                this.yearFilter = value;
                this.postData.year = this.yearFilter;
            }else{
                this.yearFilter = JSON.parse(this.years)[0].value;
            }
        },
        secondsToDhms(seconds) {
            return secondsToDhms(seconds); // view/includes/helper-js
       }
    },
    watch : {
        postData: {
            handler(newValue, oldValue) {
                this.loadData();
            },
            deep: true
    }
    }
}
</script>

