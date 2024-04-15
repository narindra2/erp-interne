@php
    $id  = $clone ? 0 :  $item->id ;
@endphp
<div class="duration-cell-component">
    <div class="section " id="timer-{{  $id  }}">
        <div class="duration-time-holder">
            <span>
                <span id="days">00:</span>
                <span id="hours">00:</span>
                <span id="minutes"> 00:</span>
                <span id="sec">00</span>
            </span>
        </div>
        {{-- <div class="dummy-div"></div> --}}
    </div>
</div>
<script>
    $(document).ready(function() {
        if (loopMinutors.item_{{  $id  }}) {
            clearInterval(loopMinutors.item_{{  $id  }})
        }
        let seconds = {{ $clone ?  0  : ($item->secondes ??  0)  }};
        $("#timer-{{  $id  }}").find("#days").text(Math.floor(seconds / (3600 * 24) ) + ":");
        $("#timer-{{  $id  }}").find("#hours").text(Math.floor( seconds % (3600 *24) /3600 ) + ":");
        $("#timer-{{  $id  }}").find("#minutes").text(Math.floor(seconds % 3600 / 60) + ":");
        $("#timer-{{  $id  }}").find("#sec").text(Math.floor(seconds % 60));

        function chrono(item_id) {
            let days = parseInt($("#timer-"+item_id).find("#days").text());
            let hours = parseInt($("#timer-"+item_id).find("#hours").text());
            let minutes = parseInt($("#timer-"+item_id).find("#minutes").text());
            let seconds = parseInt($("#timer-"+item_id).find("#sec").text());
            seconds += 1;

            if (seconds == 60) {
                minutes++;
                seconds = 00;
            }
            if (minutes == 60) {
                hours++;
                minutes = 00;
            }
            if (hours == 24) {
                days++;
                hours = 00;
            }
            days = days < 10 ? "0"+ days : days;
            hours = hours < 10 ? "0"+ hours : hours;
            minutes = minutes < 10 ? "0"+ minutes : minutes;
            seconds = seconds < 10 ? "0"+ seconds : seconds;

            $("#timer-"+item_id).find("#sec").text(seconds);
            $("#timer-"+item_id).find("#minutes").text((minutes + ":"));
            $("#timer-"+item_id).find("#hours").text((hours + ":"));
            $("#timer-"+item_id).find("#days").text((days + ":"));
        }


        $("#play-{{  $id  }}").on('click', function(){
            $("#play-{{  $id  }}").css("display","none");
            $("#pause-{{  $id  }}").css("display","");
            loopMinutors.item_{{  $id  }} = setInterval(function(){
                chrono({{  $id  }})
            }, 1000);
            // send_process({{  $id  }}, "play")
        })
        $("#pause-{{  $id  }}").on('click', function(){
            $("#pause-{{  $id  }}").css("display","none");
            $("#play-{{  $id  }}").css("display","");
            clearInterval(loopMinutors.item_{{  $id  }})
            // send_process({{  $id  }}, "pause")
        })

        let is_playing_{{  $id  }} = {{ $clone ? 0 : ($is_playing ? 1 : 0)}};

        if(is_playing_{{  $id  }}){
            $("#play-{{  $id  }}").css("display","none");
            $("#pause-{{  $id  }}").css("display","");
            loopMinutors.item_{{  $id  }} = setInterval(function(){
                chrono({{ $id }})
            }, 1000);
        }else{
            chrono({{ $id }})
        }
        // console.log(loopMinutors);
        function send_process(item_id,type){
            $.ajax({
            type: "POST",
            url: url('/project/set/processing'),
            data: {
                _token: _token,
                item_id : item_id,
                type : type
            },
            success: function(response) {

            },
            error: function(jqXhr, textStatus, errorMessage) {
                console.log(errorMessage);
            }
        });
        }

    });
</script>
