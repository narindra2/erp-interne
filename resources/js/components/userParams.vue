<template>
    <div class="card card-flush shadow-sm mb-1 mt-1">
        <div class="card-header" style="min-height: 46px;">
            <h5 class="card-title">  </h5>
            <div class="card-toolbar">
                <div class="d-flex flex-stack flex-wrap gap-4">
                    <span class="card-title fs-5">Ajouter :</span>
                    <div class="d-flex align-items-center fw-bold">
                        <select class="form-select form-select-sm " >
                            <option disabled>Ajouter</option>
                            <template v-for="(user,index) in users" :key="index">
                                <option :value="user.id">#{{ user.registration_number}} - {{ user.firstname}}</option>
                            </template>
                        </select>
                    </div>
                    <span class="card-title fs-5">Sessions :</span>
                    <div class="d-flex align-items-center fw-bold">
                        <input id="month" :value="monthSession" title= 'Mois' placeholder="Mois ..."  @change="onChangeMonth($event.target.value)" class=" form-control text-dark py-0 ps-3 w-auto ">
                    </div>
                    <div class="d-flex align-items-center fw-bold">
                        <input id="year" :value="yearSession" title= 'Année' placeholder="Année ..."  @change="onChangeYear($event.target.value)" class=" form-control text-dark  py-0 ps-3 w-auto ">
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
                <template #item-user="{ user}">
                    <div v-html="user" > </div>
                </template>
                <template #item-hours_works="{user_id,hours_works}">
                    <input type="text" class="form-control form-control-solid"  @change="updateHourWork(user_id, $event.target.value)" placeholder="1" :value="hours_works" >
                </template>
                <template #item-seuil_point="{user_id,seuil_point}">
                    <input type="text" class="form-control form-control-solid"  @change="updateSeuil(user_id ,$event.target.value)" placeholder="1" :value="seuil_point" >
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
import Tagify from '@yaireo/tagify'
import '@yaireo/tagify/dist/tagify.css'
export default {
    props : ["months" , "years"],
    data() {
        return {
            app : app,
            monthInput : null,
            yearInput : null,
            headers : [
                { text: "Nom", value: "user" ,fixed: true,},
                { text: "Seuil journalier", value: "seuil_point", },
                { text: "Heure journalier théorique", value: "hours_works",  },
            ],
            items : [
                { "user": "Curry", "hours_works": 178, "seuil_point": 77,  },
                { "user": "James", "hours_works": 180, "seuil_point": 75,  },
                { "user": "Jordan", "hours_works": 181, "seuil_point": 73,  }
            ],
            users : [],
            monthSession: JSON.parse(this.months).find((month) => month.selected == true).value,
            yearSession  : JSON.parse(this.years)[0].value,
            settings :  {
                classname: 'users-list',
                enabled       : 0,             
                position      : "text",         
                closeOnSelect : true,        
                highlightFirst: true
            }
        }
    },

    mounted() {
        this.getUserList();
        this.monthInput = new Tagify(document.querySelector('#month'),{
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
            whitelist :  JSON.parse(this.years),
            tagTextProp: 'text',
            mode : "select",
            placeholder : "Année ...",
            enforceWhitelist: true,
            keepInvalidTags: false,
            skipInvalid: true,
            dropdown : this.settings,
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(', ')
        });
    },
    
    methods: {
        getUserList(){
            axios.post(this.app.baseUrl + "/suivi/user-list").then(response => {
                if (response.data.success) {
                  this.users = response.data.users
                }
            })
        },
        updateHourWork(user_id , hours ){
            console.log("updateHourWork");
            axios.post(this.app.baseUrl + "/suivi/save/hours_days_work",{ "user_id" : user_id, "month" : this.monthSession , "year" : this.yearSession , "hours_works" : hours }).then(response => {
                if (response.data.success) {
                  
                }
            })
        },
        updateSeuil(user_id , point ){
            console.log("updateSeuil");
            axios.post(this.app.baseUrl + "/suivi/save/hours_days_work",{"user_id" : user_id, "month" : this.monthSession , "year" : this.yearSession , "seuil_point" : point }).then(response => {
                if (response.data.success) {
                    
                }
            })
        },
        updateDayworkDessi(user_id , days ){
            console.log("updateDayworkDessi");
            axios.post(this.app.baseUrl + "/suivi/save/hours_days_work",{"user_id" : user_id, "month" : this.monthSession , "year" : this.yearSession , "days_work" : days }).then(response => {
                if (response.data.success) {
                    
                }
            })
        }
    },
    watch : {
        
    }
}
</script>