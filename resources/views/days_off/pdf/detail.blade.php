<table>
    <tr>
        {{-- <th style="width :100px;"><img src="{{ public_path('demo1/media/logos/logo2.png') }}"></th> --}}
        <th><p style="padding-left: 160px"><strong>DEMANDE DE CONGÉ</strong</p></th>
    </tr>
</table>
  <p>Nom : {{ $dayOff->applicant->name }} </p>
  <p>Prénom : {{ $dayOff->applicant->firstname }} </p>
  <p>Matricule : {{ $dayOff->applicant->registration_number }}</p>
  <p>Fonction : {{ $dayOff->getApplicantJob() }}</p>
  <p style="padding-left: 20px"> - &nbsp; Type d'absence : {{ $dayOff->type->getType() }}</p>
  <p style="padding-left: 20px">- &nbsp; Date de la demande : {{ $dayOff->getDemandeDate()->format('d/m/Y') }} </p>
  <p style="padding-left: 20px">- &nbsp; Début du congé : {{ $dayOff->getStartDate()->format('d/m/Y') }} </p>
  <p style="padding-left: 20px">- &nbsp; Date de retour : {{ $dayOff->getReturnDate()->format('d/m/Y') }}</p>
  <p style="padding-left: 20px">- &nbsp; Nombre de jour du congé : {{ $dayOff->duration }} <span
          style="padding-left: 160px"> Solde congé : {{ $dayOff->applicant->nb_days_off_remaining }} </span> </p>
  <p style="padding-left: 20px">- &nbsp; Nature de la demande : {{ $dayOff->reason }}</p>
<table>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    <tr>
        <td colspan="5">Signature du salarié</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="padding-left: 160px">Signature de la Direction</td>
    </tr>
</table>
