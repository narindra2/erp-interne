<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SlackDataCapture;
use App\Jobs\SlackNotificationJob;
use App\Notifications\SlackActivityNotification;

class SlackEndpointController extends Controller
{
    private $slack;
    private $event_types_allowed = ["message"];
    private static $token = 'xoxp-3794547389856-3770793402994-3783818223793-fe0fa7120d157a7c916d5d66dbde2647';
    
    /**
     * Les documentations consulté (By developeur Narindra)
     * https://api.slack.com/apps/A03NNRK3W90/oauth?  => Mettre les autorisations
     * https://api.slack.com/apps/A03NNRK3W90/event-subscriptions?  => Choisir les evemenents a traité /abonné
     * https://api.slack.com/methods  => Expication des  evemenents n les données à poster et les reponses
     * https://packagist.org/packages/wrapi/slack  => Package doc Slack Api 
     * 
     */

    public function __construct()
    {
        $this->slack = new \wrapi\slack\slack(self::$token);
    }
    public function capture_get(Request $request)
    {
        $this->caputre_request($request->all());
        return response()->json([
            'token' => self::$token,
            'challenge' => $request->json()->get('challenge'),
            "type"=> "url_verification"
        ]);
    }
    public function capture(Request $request)
    {
        $this->caputre_request($request->all());
        return response()->json([
            'token' => self::$token,
            'challenge' => $request->json()->get('challenge'),
            "type"=> "url_verification"
        ]);
    }
    public function caputre_request($request = [])
    {
        $captured = SlackDataCapture::create(["data" => serialize($request)]);
        $data = unserialize($captured->data);
        $this->verification($data);
        $this->make_notification($data);
    }
    public function verification($data) : void
    {
        $event = get_array_value($data, "event");
        if (!$event) {
            die("no event !");
        }
        $event_types = get_array_value($event, "type");
        if (!in_array($event_types, $this->event_types_allowed)) {
            die("the event-type allowed ! ");
        }
    }
    public function make_notification($data){
        $event = get_array_value($data, "event");
        
        /** Execute notification channel  */
        $channel_id = get_array_value($event, "channel");
        if($channel_id){
            $this->send_channel_notification($event);
        }

    }
    /** Send notification  and more info */
    private function send_channel_notification($event){
        $channel_id = get_array_value($event, "channel");
        $user_id = get_array_value($event, "user" , null);
        if($user_id){
            $causer_name = $this->get_trigger($user_id);
        }
        dispatch(new SlackNotificationJob(
            $this->get_interne_members_channel($event),
            new SlackActivityNotification($causer_name ,[
                "channel_name" => $this->get_channel_name($channel_id) , 
                "type" =>  get_array_value($event, "type")])
        ));
    }
    /** Get the creator of activity from Slack API */
    private function get_trigger($user_id = "") : string {
        $response = $this->slack->users->info(["user" => $user_id]);
        $user = get_array_value($response,"user");
        if(!$user){
            return null;
        }
        $profile = get_array_value($user,"profile");
        if(!$profile){
            return null;
        }
        $real_name = get_array_value($profile,"real_name");
        $last_name = get_array_value($profile,"last_name");
        $first_name = get_array_value($profile,"first_name");
        return ($first_name ?? $last_name ) ?? $real_name ;
    }
    /** Get channel name */
    private function get_channel_name($channel_id = "") {
        $response = $this->slack->conversations->info( ["channel" => $channel_id]);
        $channel_info = get_array_value($response,"channel");
        return get_array_value($channel_info,"name" , null);
    }
    
    /** Get all user email's on this channel from Slack API  */
    public function get_interne_members_channel($event = "")
    {
        $emails = [];
        $channel = get_array_value($event, "channel");
        $user_causer_id = get_array_value($event,"user");
        $response = $this->slack->conversations->members( ["channel" => $channel]);
        $members = get_array_value($response, "members");
        foreach ($members as $user_id) {
            if($user_causer_id != $user_id){
                try {
                    $user = $this->slack->users->info(["user" => $user_id]);
                    $emails[] = $user["user"]["profile"]["email"];
                } catch (\Throwable $th) {
                    continue;
                }
            }
        }
        return $this->get_users_interne($emails);
    }

    private function get_users_interne($emails)
    {
        return User::select(["id", "email", "name", "firstname", "deleted"])->whereIn("email", $emails)->whereDeleted(0)->get();
    }
    /** Pakage -API Slack- documenation  */
    public function documentation() {
        return redirect("https://packagist.org/packages/wrapi/slack");
    }

    public  function decode_slack_event($id)
    {
        // $channel = $this->get_channel_name( "D03P1D36F7B") ;
        // $channel = $this->slack->conversations->info( ["channel" =>"D03P1D36F7B"]);
        // dd( $channel );
        $data = SlackDataCapture::find($id);
        if(!$data){
            return ["success" => false , "message" => "No data in this id"];
        }
        // dd(unserialize($data->data));
        $this->make_notification(unserialize($data->data));
        // $data = DB::table("slack_request")->find(1);
        // dd(unserialize( $data->data));
        // channnel id : C03P1HBNCE5
        // user_ids
        // "U03NG5KKJA2",
        // "U03NNPBBUV8",
        return ["success" => true , "message" => "Decode avec success ! "];
    }
    
}
