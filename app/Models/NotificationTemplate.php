<?php

namespace App\Models;

use Exception;


class NotificationTemplate 
{

    static function get_subject_info($notification){
        $subject = [];
        $subject['name'] = "on";
        $subject['profile'] = null;
        try {
            if (isset($notification->data["created_by"]) && $notification->data["created_by"]) {
                $creator = User::find($notification->data["created_by"]);
                $subject['name']    =  $creator->sortname;
                $subject['profile'] = '<img alt="Pic" src='.$creator->avatarUrl.'>' ;
            } 
        }
        catch (Exception $e) {

        }
       return $subject;
    }


    /** Event template */
    public static function dayoff_created($notification = null ,$send_to){
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject =  $subject_info['name'];
        $template["profile"] = $subject_info["profile"] ; 

        $template["title"]= "Demande de congé";
        if (isset($notification->data['applicant_id'])) {
            $applicant = User::find($notification->data['applicant_id']);
            if ($notification->data["update"]) {
                $template["sentence"] =  "$subject a mis à jour la demande de congé de ". ($notification->data["created_by"] != $applicant->id) ? "$applicant->sortname" : "lui-même";
            } else {
                $template["sentence"] =  "$subject a une nouvelle demande de congé de $applicant->sortname.";
            }
            return  $template;
        }
        $template["sentence"] = "$subject a une nouvelle demande de congé.";
        return $template;
    }

    /** Event template */
    public static function dayoff_updated_status($notification = null,$send_to){
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject =  $subject_info['name'];
        $template["profile"] = $subject_info["profile"] ; 

        $template["title"]= "Demande de congé";
        $template["sentence"] =  "$subject a mis à jour votre demande de congé ! ";
        return  $template;
    }

    public static function negative_cumulative_hour($notification = null,$send_to){
        $template = [];
        $template["title"]= "Pointage negative";
        $template["action"]= "Info";
        $template["sentence"] = "Alerte sur votre heure cumule négative : $notification->hour_cumul ! ";
        return  $template;
    }
    public static function ticket_created($notification = null,$send_to){
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject =  $subject_info['name'];
        $template["profile"] = $subject_info["profile"] ; 
       
        $template["title"]= "Ticket ";
        $template["action"]= "Ajout";
       
        $ticket = $notification->data["object"] ?? Ticket::find($notification->data["ticket_id"]);
        $template["sentence"] = "$subject a crée un nouveau ticket : {$ticket->tag()} ";
        return  $template;
    }
    public static function ticket_assigned($notification = null,$send_to){
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject =  $subject_info['name'];
        $template["profile"] = $subject_info["profile"] ; 
       
        $template["title"]= "Ticket ";
        $template["action"]= "Ajout";
       
        $ticket = $notification->data["object"] ?? Ticket::find($notification->data["ticket_id"]);
        $template["sentence"] = "$subject vous a assigné un ticket : {$ticket->tag()} ";
        return  $template;
    }  
    public static function ticket_updated($notification = null,$send_to){
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject =  $subject_info['name'];
        $template["profile"] = $subject_info["profile"] ; 
       
        $template["title"]= "Ticket ";
        $template["action"]= "Edit";
       
        $ticket = $notification->data["object"] ?? Ticket::find($notification->data["ticket_id"]);
        $template["sentence"] = "$subject  a mis à jour le ticket : {$ticket->tag()} ";
        return  $template;
    }  
    public static function contrat_changed($notification = null ,$send_to){
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject = $subject_info['name'];
      
        $template["profile"] = $subject_info["profile"] ; 
       
        $template["title"]= "Info emploi ";
        $template["action"]= "Mise à jour";
       
        $contrat_type = $notification->data["object"] ?? ContractType::find($notification->data["contract_type_id"]);

        if ($send_to->id == $notification->data["user_id"] ) {
            $template["sentence"] = "$subject a renouvelé votre type de contrat en «{$contrat_type->name }» ({$contrat_type->acronym})";
        }else{
            $user =  User::find($notification->data["user_id"]);
            $template["sentence"] = "$subject a mis à jour le type de contrat de {$user->sortname} en «{$contrat_type->name }» ({$contrat_type->acronym})" ;
        }
        return  $template;
    }  
    public static function task_assigned($notification = null ,$send_to){
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject = $subject_info['name'];
        $template["profile"] = $subject_info["profile"] ; 
        $template["title"]= "Tâche";
        $template["action"]= "Ajout";
        $task = $notification->data["object"] ?? Task::with(["section"])->find($notification->data["task_id"]);
        $template["sentence"] = "$subject vous a assigné une tâche  : « " . str_limite($task->title,15). " » - Section : {$task->section->title}" ;
        return  $template;
    }  
    public static function new_message($notification=null, $send_to) {
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject = $subject_info['name'];
        $template["profile"] = $subject_info["profile"] ; 
        $template["title"]= "Nouveau Message";
        $template["action"]= "Ajout";
        $message = $notification->data["object"] ?? Message::find($notification->data["message_id"]);
        try {
            $template["sentence"] = "$subject vous a envoyé un message  : « " . str_limite($message->content,15). " »" ;
        }
        catch (Exception $e) {
            $template['sentence'] = "";
        }
        return  $template;
    }
   

    public static function task_updated($notification = null ,$send_to){
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject = $subject_info['name'];
        $template["profile"] = $subject_info["profile"] ; 
        $template["title"]= "Tâche";
        $template["action"]= "Mise à jour";
        $template["class"]= "info";
        $task = $notification->data["object"] ?? Task::with(["section"])->find($notification->data["task_id"]);
        $updated = $notification->data["updated"];
        $template["sentence"] = "$subject a effectué une mofication du tâche  : « " . str_limite($task->title,15). " » - Section : {$task->section->title}" ;
        if (get_array_value($updated, "old_status")) {
            $old_status  = get_array_value($updated, "old_status");
            $new_status  = get_array_value($updated, "new_status");
            $template["sentence"] = $template["sentence"] ."<br>" ."<u>Status</u> : <strike>$old_status</strike> ->  $new_status   ";
        }
        if (get_array_value($updated, "old_title")) {
            $old_title  = get_array_value($updated, "old_title");
            $new_title  = get_array_value($updated, "new_title");
            $template["sentence"] = $template["sentence"] ."<br>" ."<u>Title</u> : <strike>$old_title</strike> ->  $new_title   ";
        }
        if (get_array_value($updated, "new_reponsibles")) {
            $new_reponsibles  = get_array_value($updated, "new_reponsibles");
            $template["sentence"] = $template["sentence"] ."<br>" ."<u>Ajout de</u> : $new_reponsibles ";
        }
        if (get_array_value($updated, "description")) {
            $template["sentence"] = $template["sentence"] ."<br>" ."<u>Mise à jour</u> : Déscription du tâche ...";
        }
        if (get_array_value($updated, "deadline_date")) {
            $template["sentence"] = $template["sentence"] ."<br>" ."<u>Mise à jour</u> : Date de deadline";
        }
        if (get_array_value($updated, "deleted")) {
            $template["sentence"] = $template["sentence"] ."<br>" ."<u>Action</u> : Suppression du tâche";
        }
        return  $template;
    }  
    public static function task_commented($notification= null, $send_to) {
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject = $subject_info['name'];
        $template["profile"] = $subject_info["profile"] ; 
        $template["title"]= "Tâche commenté";
        $template["action"]= "Ajout";
        $template["class"]= "info";
        $task = $notification->data["object"] ?? Task::find($notification->data["task_id"]);
        $string = "a commenté " ;
        if ($task->creator == $notification->data["created_by"]) {
            $string = "a ajouté une commentaire dans " ;
        }
        $template["sentence"] = "$subject $string la tâche  : « " . str_limite($task->title,15). " » - Section : {$task->section->title}" ;
        return  $template;
    }
    public static function task_file_added($notification= null, $send_to) {
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject = $subject_info['name'];
        $template["profile"] = $subject_info["profile"] ; 
        $template["title"]= "Tâche commenté";
        $template["action"]= "Ajout";
        $template["class"]= "info";
        $task = $notification->data["object"] ?? Task::with(["section"])->find($notification->data["task_id"]);
        $string = "a mis " ;
        if ($task->creator == $notification->data["created_by"]) {
            $string = "a ajouté " ;
        }
        $template["sentence"] = "$subject $string un fichier dans la tâche  : « " . str_limite($task->title,15). " » - Section : {$task->section->title}" ;
        return  $template;
    }
    public static function task_section_created($notification= null, $send_to) {
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject = $subject_info['name'];
        $template["profile"] = $subject_info["profile"] ; 
        $template["title"]= "Tâche section";
        $template["action"]= "Ajout";
        $section = $notification->data["object"] ?? TaskSection::find($notification->data["section_id"]);
        $template["sentence"] = "$subject a vous ajouter dans la section tâchela  : « " . str_limite($section->title,15). " »" ;
        return  $template;
    }
    public static function task_section_updated($notification= null, $send_to) {
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject = $subject_info['name'];
        $template["profile"] = $subject_info["profile"] ; 
        $template["title"]= "Tâche section";
        $section = $notification->data["object"] ?? TaskSection::find($notification->data["section_id"]);
        if ($section->deleted) {
            $template["action"]= "Suppression";
            $template["class"]= "danger";
            $template["sentence"] = "$subject a supprimé la section tâche : « " . str_limite($section->title,15)." »";
        }else{
            $template["action"]= "Ajout";
            $template["class"]= "warning";
            $updated = $notification->data["updated"];
            $new = get_array_value($updated, "new_title");
            $old = get_array_value($updated, "old_title");
            $template["sentence"] = "$subject a modifié le non du section tâche « $old » en : « " . str_limite($new,15)." »";
        }
        return  $template;
    }
    public static function new_suivi_add($notification= null, $send_to) {
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject = $subject_info['name'];
        $template["profile"] = $subject_info["profile"]; 
        $template["title"]= "Dossier";
        $template["action"]= "Ajout";
        $suivi_item = $notification->data["object"] ?? SuiviItem::find($notification->data["suivi_item_id"]);
        $template["sentence"] = "Un nouveau dossier  vous a été attribué par {$subject}  nommé «{$suivi_item->suivi->folder_name}»" ;
        return  $template;
    }
    public static function user_pause_prod($notification= null, $send_to) {
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $template["title"]= "Productivités";
        $template["action"]= "Action";
        $template["profile"] = $subject_info["profile"]; 
        $template["sentence"]  = $notification->data["message"] ;
        return  $template;
    }
    public static function purchase_created($notification= null, $send_to) {
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject_name = $subject_info['name'];
        $template["title"]= "Demande d'achat";
        $template["action"]= "Ajout";
        $template["profile"] = $subject_info["profile"]; 
        $template["sentence"] = "Une nouvelle demande  d' achat est ajouté  par   {$subject_name} " ;
        return  $template;
    }
    public static function purchase_statut_update($notification= null, $send_to) {
        
        $template = [];
        $subject_info = self::get_subject_info($notification);
        $subject_name = $subject_info['name'];
        $template["title"]= "Demande d'achat";
        $template["action"]= "Mise à jour";
        $template["profile"] = $subject_info["profile"];
        $purchase = $notification->data["object"] ?? Purchase::with(['author'])->find($notification->data['purhcase_id']);
       
        $updated = $notification->data["updated"] ?? [];
        $old_status  = get_array_value($updated, "old_status");
        $new_status  = get_array_value($updated, "new_status");

        $template["sentence"] = "{$subject_name} a mis  « <strike>$old_status</strike> ->{$new_status} » la demande d' achat de «{$purchase->author->sortname}»" ;
        return  $template;
    }
}