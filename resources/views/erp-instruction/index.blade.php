<x-base-layout>
    </style>
    <div class="row">
        <div class="col-2">
            <div class="card card-custom d-flex flex-grow-1">
                <div class="card-body flex-grow-1">
                    <h4 class="text-center mb-10">Sommaire</h4>
                    <p>
                        <a href="#imag-1" class="text-dark summary">Image 1 : Absence non envisagé – Retards – Congé</a>
                    </p>
                    <p>
                        <a href="#imag-2" class="text-dark summary">Image 2 : Permission – Récupération – Demande d'attestation</a>
                    </p>
                    <p>
                        <a href="#imag-3" class="text-dark summary">Image 3 : Permission d'urgence – Permission médicale – Congé d'urgence</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-10">
            <div class="card card-custom d-flex flex-grow-1">
                <div class="card-body flex-grow-1 no-copy">
                    <h2 class="text-center mb-10">Fonctionnement en Interne</h2>
                    <div id="imag-1">
                        <h6 class="mb-2 text-center" style="text-decoration: underline;">Absence non envisagé – Retards – Congé</h6>
                        <img src="{{ asset("images/F-1.png") }}" >
                     
                    </div>
                    <div id="imag-2">
                        <h6 class="mb-2 text-center" style="text-decoration: underline;"> Permission – Récupération – Demande d'attestation</h6>
                        <img src="{{ asset("images/F-2.png") }}" >
                    </div>
                    <div id="imag-3">
                        <h6 class="mb-2 text-center" style="text-decoration: underline;">Permission d'urgence – Permission médicale – Congé d'urgence</h6>
                        <img src="{{ asset("images/F-3.png") }}" >
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        img {
            width: 100%;
        }
    </style>
</x-base-layout>