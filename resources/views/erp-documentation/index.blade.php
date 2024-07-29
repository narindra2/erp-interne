<x-base-layout>

    <style>
        .no-copy {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .chapt {
            display: none;
            width: 200px;
        }
    </style>

    <div class="row">
        <div class="col-3">
            <div class="card card-custom d-flex flex-grow-1">
                <div class="card-body flex-grow-1">
                    <h4 class="text-center mb-10">Sommaire</h4>
                    <p>
                        <a href="#chapt-1" class="text-dark summary">Chapitre I : Dispositions générales</a>
                    </p>
                    <p>
                        <a href="#chapt-2" class="text-dark summary">Chapitre II : Durée – Révision – Modification – Dénonciation</a>
                    </p>
                    <p>
                        <a href="#chapt-3" class="text-dark summary">Chapitre III : Liberté d'opinion</a>
                    </p>
                    <p>
                        <a href="#chapt-4" class="text-dark summary">Chapitre IV : Embauche et engagement à l'essai</a>
                    </p>
                    <p>
                        <a href="#chapt-5" class="text-dark summary">Chapitre V : Formations Professionnelles</a>
                    </p>
                    <p>
                        <a href="#chapt-6" class="text-dark summary">Chapitre VI : Administratifs et RH</a>
                    </p>
                    <p>
                        <a href="#chapt-7" class="text-dark summary">Chapitre VII : L'organisation de </a>
                    </p>
                    <p>
                        <a href="#chapt-8" class="text-dark summary">Chapitre VIII : Les horaires de travail</a>
                    </p>
                    <p>
                        <a href="#chapt-9" class="text-dark summary">Chapitre IX : Les congés, la suspension et rupture du contrat du travail</a>
                    </p>
                    <p>
                        <a href="#chapt-10" class="text-dark summary">Chapitre X : Promotion</a>
                    </p>
                    <p>
                        <a href="#chapt-11" class="text-dark summary">Chapitre XI : Sanctions</a>
                    </p>
                    <p>
                        <a href="#chapt-12" class="text-dark summary">Chapitre XII : Utilisation de matériels de la société</a>
                    </p>
                    <p>
                        <a href="#chapt-13" class="text-dark summary">Chapitre XIII : La discipline générale</a>
                    </p>
                    <p>
                        <a href="#chapt-14" class="text-dark summary">Chapitre XIV : Les hiérarchies de sanction et leurs modes d'applications</a>
                    </p>
                    <p>
                        <a href="#chapt-15" class="text-dark summary">Chapitre XIV : Le règlement spécifique</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-9">
            <div class="card card-custom d-flex flex-grow-1">
                <!--begin::Body-->
                <div class="card-body flex-grow-1 no-copy">
                    <h2 class="text-center mb-10">Règlement Intérieur</h2>
                    <h4 class="text-center mb-10">Préambule</h4>
                    <p>
                        Afin d’établir pour le Personnel de la « société », un statut garantissant l’indépendance, la dignité,
                        l’épanouissement de chacun, et l’intégrité physique du Personnel et de sa famille, afin de préserver
                        les droits inaliénables du travailleur et de garantir l’équilibre social dans l’Entreprise.
                    </p>
                    <p>
                        Conscients que l’avenir de la société est lié à la performance de son personnel, à l’amélioration de
                        ses compétences pour donner satisfaction à ses clients.
                    </p>
        
                    <p>
                        En vue d’assurer l’harmonie nécessaire à la bonne marche des services, à la défense des intérêts
                        communs de la profession et au rendement de ses activités.
                    </p>
        
                    <p>
                        La présente Convention a été adoptée.
                    </p>
                    @php
                        $chapter_count = 15;
                    @endphp
                    @for ($i = 1; $i <= $chapter_count; $i++)
                        <div id="{{ "chapt-". $i }}"></div>
                        @include('erp-documentation.chapters.chapter' . $i, ['agency' => $agency])
                    @endfor
                </div>
                <!--end::Body-->
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            $(document).bind("contextmenu",function(e) {
                e.preventDefault();
            });

            $(document).keydown(function(e){
                if(e.which === 123){
                    return false;
                }
                else if(event.ctrlKey && event.shiftKey && event.keyCode==73) {
                    return false;  //Prevent from ctrl+shift+i
                } 
            });

            $(document).ready(function () {
                $(".summary").on("click", function() {
                    var id = $(this).attr("href");
                    
                    $(id).animate({scrollTop: $(id)[0].scrollHeight}, 2000);
                });
            });

        </script>
    @endsection
</x-base-layout>