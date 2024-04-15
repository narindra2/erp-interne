    <div id="fullcalendar-meeting-room"></div>
    <link rel="stylesheet" href="{{ asset('library/fullcalendar/main.min.css') }}">
    <script src="{{ asset('library/fullcalendar/main.min.js') }}"></script>
    <script src="{{ asset('library/fullcalendar/locales/fr.js') }}"></script>
    <script>
        $(document).ready(function() {
            var green =  KTUtil.getCssVariableValue("--kt-success-active");
            var red =  KTUtil.getCssVariableValue("--kt-danger-active");
            var calendarEl = document.getElementById("fullcalendar-meeting-room");

            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay"
                },
                locale: 'fr',
                initialDate: "{{ $today }}",
                navLinks: true, // can click day/week names to navigate views
                selectable: true,
                businessHours: true, // display business hours
                editable: false,
                selectable: false,
                select: function(arg) {
                    console.log("select");
                },
                eventClick: function(arg) {
                    console.log("eventClick");
                },
                dayMaxEvents: true, // allow "more" link when too many events
                events: @json($horaires),
            });

            calendar.render();
            setTimeout(() => {
                $(".fc-dayGridMonth-button").click()
            }, 500);
        });
    </script>
