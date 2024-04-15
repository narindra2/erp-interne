<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $table = "tickets";
    protected $fillable = [
        'description',
        'type_id',
        'assign_to',
        'author_id',
        'proprietor_id',
        'status_id',
        'urgence_id',
        'resolve_by',
        'resolve_date'
    ];

    public function autor()
    {
        return $this->belongsTo(User::class, "author_id");
    }
    public function owner()
    {
        return $this->belongsTo(User::class, "proprietor_id");
    }
    public function resolver()
    {
        return $this->belongsTo(User::class, "resolve_by");
    }
    public function status()
    {
        return $this->belongsTo(TicketStatus::class, "status_id");
    }

    public function urgence()
    {
        return $this->belongsTo(TicketUrgence::class, "urgence_id");
    }

    public function getStatusHtmlAttribute()
    {
        return "<span class='badge badge-light-{$this->status->class}'>" . trans("lang.{$this->status->name}") . "</span>";
    }

    public function getUrgenceHtmlAttribute()
    {
        return "<span class='badge badge-light-{$this->urgence->class}'>" . trans("lang.{$this->urgence->name}") . "</span>";
    }
    public function is_resolved()
    {
        return in_array($this->status_id, TicketStatus::$_RESOLVED) ? true : false;
    }
    public function tag()
    {
        return "#00$this->id";
    }
    public function responsibles()
    {
        if ($this->assign_to) {
            return User::withOut(["userJob"])->findMany(explode(",", $this->assign_to));
        }
        return [];
    }
    public function getResponsiblesAttribute()
    {
        return $this->responsibles();
    }

    public function scopeGetDetail($query, $options = [], User $user)
    {
        $query->with([
            "autor" => function ($q1) {
                $q1->without(["userJob"]);
            }, "owner" => function ($q2) {
                $q2->without(["userJob"]);
            }, "resolver" => function ($q3) {
                $q3->without(["userJob"]);
            }, "status",  "urgence"
        ]);
        if (!$user->isIT() &&  !$user->isAdmin()  && !$user->isHR()) {
            $query->where(function ($q) use ($user) {
                $q->where('proprietor_id', $user->id);
                $q->orWhere('author_id', $user->id);
                $q->orWhere(function ($q2) use ($user) {
                    $q2->whereRaw('FIND_IN_SET(' . $user->id . ',assign_to)');
                });
            });
        }
        $status_id = get_array_value($options, "status_id",);
        if ($status_id && $status_id  != "all") {

            $query->where("status_id", $status_id); //where  status definied
        } elseif ($status_id  == "all") {
            $query->where("status_id", "<>", 0); /// get all status
        } else {
            $query->whereNotIn("status_id", TicketStatus::$_RESOLVED); // exlude cloturé,resolue  on first
        }
        $urgence_id = get_array_value($options, "urgence_id");
        if ($urgence_id) {
            $query->where("urgence_id", $urgence_id);
        }
        $proprietor_id = get_array_value($options, "proprietor_id");
        if ($proprietor_id) {
            $query->where("proprietor_id", $proprietor_id);
        }
        $assign_to = get_array_value($options, "assign_to");
        if ($assign_to) {
            $query->whereRaw(' FIND_IN_SET(' . $assign_to . ',assign_to)');
        }
        return $query->whereDeleted(0)->orderBy('urgence_id', 'DESC')->orderBy('status_id', 'ASC')->orderBy('created_at', 'DESC');
    }

    public static function createFilter(User $user)
    {
        $filters =  $from =  $assign_to = [];
        $usersJob = Department::with(["user"])->getAllEmployee()->get();
        foreach ($usersJob as $userJob) {
            try {
                if (!$userJob->user->deleted) { // remove all user deleted
                    $from[] = ["value" => $userJob->user->id, "text" => $userJob->user->sortname];
                    if ($userJob->department_id == Department::$_IT) {
                        $assign_to[] = ["value" => $userJob->user->id, "text" => $userJob->user->sortname];
                    }
                }
            } catch (Exception $e) {
            }
        }
        if ($user->isAdmin() || $user->userJob->department_id == Department::$_IT) {
            $filters[] = [
                "label" => "Ticket de",
                "name" => "proprietor_id",
                "type" => "select",
                'attributes' => [
                    "data-hide-search" => "false",
                    "data-allow-clear" => "true",
                ],
                'options' => $from,
            ];

            $filters[] = [
                "label" => "Assigné à",
                "name" => "assign_to",
                'attributes' => [
                    "data-hide-search" => "false",
                    "data-allow-clear" => "true",
                ],
                "type" => "select",
                'options' => $assign_to,
            ];
        }

        $filters[] = [
            "label" => "Statut",
            "name" => "status_id",
            "type" => "select",
            'options' => TicketStatus::drop(),
        ];
        $filters[] = [
            "label" => "Priorité",
            "name" => "urgence_id",
            "type" => "select",
            'options' => TicketUrgence::drop(),
        ];

        return $filters;
    }
}
