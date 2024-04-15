@php
    $auth = auth()->user();;
@endphp
@if (auth()->check())
    <button id="in" href="#" @if ($auth->last_check == 'in') style ="display: none" @endif
        class="check-user-web hide-it btn btn-sm h-30px btn-flex btn-light  btn-light-success">
        @include('partials.general._button-indicator', ['label' => 'Check in &nbsp;<i
            class="fas fa-user-check "></i>',"message" => "Analysing ..."])
    </button>
    <button id="pause" @if ($auth->last_check == 'pause' || $auth->last_check == 'out') style ="display: none" @endif href="#"
        class="check-user-web btn btn-sm  h-30px btn-flex btn-light  btn-light-warning">
        @include('partials.general._button-indicator', ['label' => 'Pause &nbsp; <i
            class="fas fa-user-times"></i>',"message" => "Analysing ..."])
    </button id="">
    <button id="out" @if ($auth->last_check == 'out') style ="display: none" @endif href="#"
        class="check-user-web btn btn-sm h-30px btn-flex btn-light  btn-light-primary">
        @include('partials.general._button-indicator', ['label' => 'Check out &nbsp;<i
            class="fas fa-user-alt-slash"></i>',"message" => "Analysing ..."])
    </button>
    <button href="#" class="btn btn-sm h-30px btn-flex btn-light  btn-light-danger menu-dropdown"
        data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
        out
    </button>
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800  fw-bold py-4 w-250px fs-6 mt-3"
        data-kt-menu="true" style="">
        <a id="fin" href="#" class="check-user-web  btn-sm h-30px btn-flex  btn-light-primary "
            data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            @include('partials.general._button-indicator', ['label' => 'Quitter et se deconneter ',"message" =>
            "Analysing ..."])
        </a>
        <div class="separator my-1"></div>
        @php
            echo modal_anchor(url('/user/info'), 'Voir mes historiques', ['class' => 'btn-sm h-30px btn-flex  btn-light-primary', 'data-modal-lg' => true, 'title' => 'Mes historiques']);
        @endphp
    </div>
    <script>
        $(document).ready(function() {
            function chrono() {
                let hours = parseInt($("#hours").text());
                let minutes = parseInt($("#minutes").text());
                minutes += 1;
                if (minutes == 60) {
                    hours++;
                    minutes = 0;
                }
                $("#hours").text(("0" + hours).slice(-2));
                $("#minutes").text(("0" + minutes).slice(-2));
            }

            $.ajax({
                type: "get",
                url: url('/user_chrono'),
                data: {
                    _token: _token
                },
                success: function(response) {
                    $("#minutes").text(response.data % 60);
                    $("#hours").text(Math.floor(response.data / 60));
                    $("#timer").css('display', 'block');
                    chrono();
                },
                error: function(jqXhr, textStatus, errorMessage) {
                    console.log(errorMessage);
                }
            });
            intervalID = setInterval(chrono, 1000 *60);

            var content = document.querySelector("#kt_content");
            var btn_in = document.querySelector("#in");
            var btn_pause = document.querySelector("#pause");
            var btn_out = document.querySelector("#out");

            /** Init blocker page*/
            var blockMessage = '';
            var blockAppPage = new KTBlockUI(content, {
                message: blockMessage,
            });
            /** Block page if user is not check in*/
            <?php  if (!$auth->last_check || $auth->last_check !== "in") { ?>
                clearInterval(intervalID);
                var last_ckeck = "Verouiller"
                blockMessage = '<div class="blockui-message">  ' + last_ckeck +
                    ' &nbsp; <i class="fas fa-user-lock"></i>  </div>'
                blockAppPage.options.message = blockMessage
                if (!blockAppPage.isBlocked()) {
                    blockAppPage.block();
                    $(".blockui-overlay ").css("cursor", "not-allowed")
                }
                
            <?php } ?>

            $(".check-user-web").on("click", function() {
                var _target = $(this)
                var check_event = _target.attr("id")
                _target.attr('data-kt-indicator', "on");
                $.ajax({
                    url: "{{ url('/user/check-timer') }}",
                    type: "post",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        'check_event': check_event
                    },
                    success: function(response) {
                        if (!response.success) {
                            _target.attr('data-kt-indicator', "off");
                            return toastr.error(response.message);
                        }
                        toastr.success(response.message)
                        _target.removeAttr('data-kt-indicator');
                        if (response.to_hide_btn) {
                            response.to_hide_btn.forEach(button =>
                                $("#" + button).css("display", "none")
                            )
                        }
                        if (response.to_active_btn) {
                            response.to_active_btn.forEach(button =>
                                $("#" + button).css("display", "")
                            )
                        }
                        blockMessage =
                            '<div class="blockui-message"> Verouiller &nbsp; <i class="fas fa-user-lock"></i>  </div>'
                        blockAppPage.options.message = blockMessage
                        if (response.block_page) {
                            if (!blockAppPage.isBlocked()) {
                                blockAppPage.block();
                                $(".blockui-overlay ").css("cursor", "not-allowed")
                            }
                        } else {
                            if (blockAppPage.isBlocked()) {
                                blockAppPage.release();
                            }
                        }
                        if (response.event === "fin") {
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                        if (response.clear_chrono) {
                            clearInterval(intervalID);
                        } else {
                            setInterval(chrono, 1000  *60);
                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                    }
                });
            })
        })
    </script>
@endif
