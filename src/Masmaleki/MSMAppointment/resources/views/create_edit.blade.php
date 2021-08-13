<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Appointments Manager</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="/msm/appointments">Appointment Manager</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="/msm/appointments">Appointments List</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="/msm/appointments/create">Add Appointment</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    @if ($errors->any())
        <div class="container py-5">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @php($appointment = session()->get('appointment'))
    @if (isset($appointment))
        <div class="container py-5">
            <div class="row">
                <div class="col-12">
                    <div class="card p-2 border-0">
                        <div class="alert alert-success">
                            @lang('general.appointment-success-message')
                            <br>
                            <strong>
                                <a href="{{ $appointment->link }}" target="_blank" class="text-primary" rel="noopener noreferrer">@lang('general.Click To See')</a>
                            </strong>
                            <br>
                            <span>@lang('general.Your Appointment ID'):</span>
                            <strong>
                                {{ $appointment->uuid }}
                            </strong>
                            <br>
                            <span class="text-muted">
                                @lang('general.You can cancel or update your appoinment with this code').
                            </span>
                        </div>
                        
                        <div class="row px-3">
                            <div class="col-md-6 d-flex flex-column">
                                <span class="py-2">
                                    <strong>@lang('general.Name'):</strong> {{ $appointment->client_name }}
                                </span>
                                <span class="py-2">
                                    <strong>@lang('general.Email'):</strong> {{ $appointment->client_email }}
                                </span>
                                <span class="py-2">
                                    <strong>@lang('general.Phone'):</strong> {{ $appointment->client_phone }}
                                </span>
                                <span class="py-2">
                                    <strong>@lang('general.Appointment ID'):</strong> {{ $appointment->uuid }}
                                </span>
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <span class="py-2">
                                    <strong>@lang('general.Selected Person'):</strong> {{ $appointment->user->name }}
                                </span>
                                <span class="py-2">
                                    <strong>@lang('general.Start From'):</strong> {{ Carbon\Carbon::parse($appointment->start_date)->toDateTimeString() }}
                                </span>
                                <span class="py-2">
                                    <strong>@lang('general.Ends At'):</strong> {{ Carbon\Carbon::parse($appointment->start_date)->addHour()->toDateTimeString() }}
                                </span>
                            </div>
                        </div>
                        <hr>
                        <div class="row px-3">    
                            <div class="col-12">
                                <h5>{{ $appointment->name }}</h5>
                                <p>{{ $appointment->client_description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else

        <form action="/msm/appointments" method="POST" id="appointment-form" class="container py-2">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="card border-0">
                        <div class="card-body">
                            <div id="step-1" class="steps">
                                <h5 class="card-title">@lang('general.Choose the person or enter your appointment ID')</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-8">
                                        <div class="form-group">
                                            <label for="appointment_uuid">@lang('general.Appointment ID')</label>
                                            <input type="text" minlength="8" maxlength="8" class="form-control" id="appointment_uuid" placeholder="@lang('general.Appointment ID')">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group pt-1">
                                            <label></label>
                                            <button type="button" onclick="searchAppointment()" class="btn btn-sm btn-primary" id="search_appointment_uuid">@lang('general.Search')</button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                @foreach ($users as $user)
                                    <button 
                                        type="button"
                                        data-username="{{ $user->name }}" 
                                        data-id="{{ $user->id }}" 
                                        onclick="selectUser('{{ $user->id }}', '{{ $user->name }}')"
                                        id="user-{{ $user->id }}" 
                                        class="hover-gray-bg w-100 d-block mb-3 border-0 p-3"
                                    >
                                        <div class="d-flex align-items-center">
                                            <div class="ml-5 w-100 text-left">
                                                <h4 class="mb-0 mt-0">{{ $user->name }}</h4> 
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                                <input type="hidden" id="user_id" name="user_id">
                            </div>


                            <div class="steps" id="step-2">
                                <h5 class="card-title">@lang('general.Select the time')</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date">@lang('general.Date')</label>
                                            <input type="text" placeholder="@lang('general.Date')" autocomplete="off" name="start_date" class="form-control" id="start_date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_time">@lang('general.Time')</label>
                                            <input type="text" placeholder="@lang('general.Time')" autocomplete="off" name="start_time" class="form-control" id="start_time" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col text-right">
                                        <a onclick="goToStep('#step-1')" class="btn btn-danger btn-sm text-white">@lang('general.Back')</a>
                                        <a onclick="goToStep('#step-3')" class="btn btn-primary btn-sm text-white">@lang('general.Next')</a>
                                    </div>
                                </div>
                            </div>


                            <div class="steps" id="step-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="subject">@lang('general.Subject')</label>
                                            <input type="text" autocomplete="nope" name="name" rows=5 placeholder="@lang('general.Subject')" class="form-control" id="subject" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="client_description">@lang('general.Description')</label>
                                            <textarea name="client_description" rows=5 placeholder="@lang('general.Description')" class="form-control" id="client_description" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col text-right">
                                        <a onclick="goToStep('#step-2')" class="btn btn-danger btn-sm text-white">@lang('general.Back')</a>
                                        <a onclick="goToStep('#step-4')" class="btn btn-primary btn-sm text-white">@lang('general.Next')</a>
                                    </div>
                                </div>
                            </div>


                            <div class="steps" id="step-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="client_name">@lang('general.Your Name')</label>
                                            <input type="text" placeholder="@lang('general.Your Name')" autocomplete="name" name="client_name" class="form-control" id="client_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="client_email">@lang('general.Email address')</label>
                                            <input type="email" placeholder="@lang('general.Email address')" autocomplete="email" name="client_email" class="form-control" id="client_email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="client_phone">@lang('general.Phone')</label>
                                            <input type="text" placeholder="@lang('general.Phone')" name="client_phone" autocomplete="tel" class="form-control" id="client_phone" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col text-right">
                                        <a onclick="goToStep('#step-3')" class="btn btn-danger btn-sm text-white">@lang('general.Back')</a>
                                        <a onclick="goToStep('#step-5')" class="btn btn-primary btn-sm text-white">@lang('general.Next')</a>        
                                    </div>
                                </div>
                            </div>


                            <div class="steps" id="step-5">
                                <div class="row px-3">
                                    <div class="col-md-6 d-flex flex-column">
                                        <span class="py-2">
                                            <strong>@lang('general.Name'):</strong> <span id="show_name"></span>
                                        </span>
                                        <span class="py-2">
                                            <strong>@lang('general.Email'):</strong> <span id="show_email"></span>
                                        </span>
                                        <span class="py-2">
                                            <strong>@lang('general.Phone'):</strong> <span id="show_phone"></span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 d-flex flex-column">
                                        <span class="py-2">
                                            <strong>@lang('general.Selected Person'):</strong> <span id="show_user"></span>
                                        </span>
                                        <span class="py-2">
                                            <strong>@lang('general.Start From'):</strong> <span id="show_date"></span> <span id="show_time"></span>
                                        </span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row px-3">    
                                    <div class="col-12">
                                        <h5 id="show_subject"></h5>
                                        <p id="show_description"></p>
                                    </div>
                                </div>    
                                <div class="row">
                                    <div class="col text-right">
                                        <a onclick="goToStep('#step-4')" class="btn btn-danger btn-sm text-white">@lang('general.Back')</a>
                                        <button type="submit" class="btn btn-primary btn-sm text-white">@lang('general.Send')</button>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="steps" id="delete-or-update">
                                <div class="row">
                                    <div class="col-10">
                                        <h5 class="card-title">@lang('general.Here is your appointment details')</h5>
                                    </div>
                                    <div class="col-2">
                                        <a onclick="goToStep('#step-1')" class="text-dark">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-dark" width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>    
                                        </a>
                                    </div>
                                </div>
                                <hr>
                                <div class="alert alert-success">
                                    <strong>
                                        <a  target="_blank" class="text-primary show_link" rel="noopener noreferrer">@lang('general.Click To See')</a>
                                    </strong>
                                    <br>
                                    <span>@lang('general.Your Appointment ID'):</span>
                                    <strong>
                                        <span class="show_uuid"></span>
                                    </strong>
                                </div>
                                
                                <div class="row px-3">
                                    <div class="col-md-6 d-flex flex-column">
                                        <span class="py-2">
                                            <strong>@lang('general.Name'):</strong> <span class="show_client_name"></span>
                                        </span>
                                        <span class="py-2">
                                            <strong>@lang('general.Email'):</strong> <span class="show_client_email"></span>
                                        </span>
                                        <span class="py-2">
                                            <strong>@lang('general.Phone'):</strong> <span class="show_client_phone"></span>
                                        </span>
                                        <span class="py-2">
                                            <strong>@lang('general.Appointment ID'):</strong> <span class="show_uuid"></span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 d-flex flex-column">
                                        <span class="py-2">
                                            <strong>@lang('general.Selected Person'):</strong> <span class="show_user"></span>
                                        </span>
                                        <span class="py-2">
                                            <strong>@lang('general.Start From'):</strong> <span class="show_start_date"></span>
                                        </span>
                                        <span class="py-2">
                                            <strong>@lang('general.Ends At'):</strong> <span class="show_end_date"></span>
                                        </span>
                                    </div>
                                </div>
                                <hr>
                                <div class="row px-3">    
                                    <div class="col-12">
                                        <h5 class="show_subject"></h5>
                                        <p class="show_description"></p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col text-left">
                                        <a onclick="goToStep('#step-delete')" class="btn btn-danger btn-sm text-white">@lang('general.Cancel this appointment')</a>
                                    </div>
                                    <div class="col text-right">
                                        <a onclick="goToUpdate()" class="btn btn-primary btn-sm text-white">@lang('general.Edit the appointment details')</a>
                                    </div>
                                </div>
                            </div>


                            <div class="steps pb-5" id="step-delete">
                                <div class="row px-3">    
                                    <div class="col-12">
                                        <h5>@lang('general.Are you sure?')</h5>
                                        <p>@lang('general.Your appointment will be canceled after confirming')</p>
                                    </div>
                                </div>    
                                
                                <div class="row">
                                    <div class="col text-left">
                                        <a onclick="goToStep('#delete-or-update')" class="btn btn-danger btn-sm ml-md-3 mr-md-5 text-white">@lang('general.No dont delete it')</a>
                                        <a onclick="setDeleteFormAction()" class="btn btn-outline-danger btn-sm text-danger">@lang('general.Yes delete it')</a>
                                    </div>
                                </div>
                            </div>

                            <div class="steps" id="step-update">
                                <div class="row">
                                    <div class="col-10">
                                        <h5 class="card-title">@lang('general.Select the time')</h5>
                                    </div>
                                    <div class="col-2">
                                        <a onclick="goToStep('#delete-or-update')" class="text-dark">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-dark" width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>    
                                        </a>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="update_start_date">@lang('general.Date')</label>
                                            <input type="text" placeholder="@lang('general.Date')" autocomplete="off" name="update_start_date" class="form-control" id="update_start_date">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="update_start_time">@lang('general.Time')</label>
                                            <input type="text" placeholder="@lang('general.Time')" autocomplete="off" name="update_start_time" class="form-control" id="update_start_time">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col text-right">
                                        <a onclick="goToStep()" class="btn btn-danger btn-sm text-white">@lang('general.Cancel')</a>
                                        <a onclick="setUpdateFormAction()" class="btn btn-primary btn-sm text-white">@lang('general.Save')</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
        <form action="" method="post" id="delete-form">
            @csrf
            @method('DELETE')
        </form>
        <form action="" method="post" id="update-form">
            @csrf
            @method('PUT')
            <input type="hidden" class="start_date" name="start_date">
            <input type="hidden" class="start_time" name="start_time">
        </form>
    @endif
    <script  src="https://code.jquery.com/jquery-3.6.0.min.js">
    </script>
    <script  src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js">
    </script>
    <script  src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js" integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js" ></script>
    <script>

        var disableDates = @json($disableDates);

        $('.steps').hide();
        goToStep('#step-1');

        $(document)
        .on('change','#start_date', function () {
            setTimeCalendar($(this).val(),'#start_time');
        })
        .on('change','#update_start_date', function () {
            setTimeCalendar($(this).val(),'#update_start_time');
        })
        .on('change', 'input', function () {
            $('#show_name').html($('#client_name').val());
            $('#show_email').html($('#client_email').val());
            $('#show_phone').html($('#client_phone').val());
            $('#show_username').html(window.selectedUserName);
            $('#show_date').html($('#start_date').val());
            $('#show_time').html($('#start_time').val());
            $('#show_subject').html($('#subject').val());
            $('#show_description').html($('#client_description').val());
        });

        function searchAppointment() {
            appointmentUUID = $('#appointment_uuid').val();
            if (appointmentUUID != null && appointmentUUID != '') {
                $.ajax({
                    type: 'post',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'uuid': appointmentUUID
                    },
                    dataType: "json",
                    url: '/msm/appointment/find-by-uuid',
                    beforeSend: function () {
                        $("#preloader").fadeIn().html('Searching ...');
                    },
                    success: function (result) {
                        $("#preloader").fadeOut();
                        if (result.type === 'error') {
                            toastr.error(result.msg, result.title);
                        } else if (result.type === 'success') {
                            window.appointmentUUID = appointmentUUID;
                            window.appointmentUserId = result.appointment.appointment_user_id;
                            $('#delete-or-update .show_client_name').html(result.appointment.client_name);
                            $('#delete-or-update .show_client_email').html(result.appointment.client_email);
                            $('#delete-or-update .show_client_phone').html(result.appointment.client_phone);
                            $('#delete-or-update .show_user').html(result.appointment.user_name);
                            $('#delete-or-update .show_start_date').html(result.appointment.start_date);
                            $('#delete-or-update .show_end_date').html(result.appointment.end_date);
                            $('#delete-or-update .show_subject').html(result.appointment.name);
                            $('#delete-or-update .show_description').html(result.appointment.client_description);
                            $('#delete-or-update .show_uuid').html(result.appointment.uuid);
                            $('#delete-or-update .show_link').attr('href',result.appointment.link);
                            goToStep('#delete-or-update');
                        }
                    }
                });
            }
        }

        function selectUser(userId, userName) {
            console.log(userId, userName);
            window.selectedUserName = userName;
            setDateCalendar(userId, '#start_date');
            goToStep('#step-2');
        }

        function goToUpdate() {
            setDateCalendar(window.appointmentUserId, '#update_start_date');
            goToStep('#step-update');
        }

        function setDateCalendar(userId, inputId) {
            $('#user_id').val(userId);

            $(inputId).datetimepicker({
                format:'d/m/Y',
                minDate: 0,
                disabledWeekDays: [0,6],
                todayButton:true,
                timepicker:false
            });
        }

        function setTimeCalendar(date, inputId) {
            let disabledHours;
            let allowTimes = [];
            let userId = $('#user_id').val();

            if (disableDates[userId][date] != null && disableDates[userId][date] != undefined) {
                disabledHours = disableDates[userId][date];
            } else {
                disabledHours = [];
            }

            for (let index = 10; index < 19; index++) {
                allowTimes[index] = index + ':00';
                if (disabledHours.includes(n(index))) {
                    allowTimes = removeItemOnce(allowTimes, index + ':00')
                }
            }

            allowTimes = allowTimes.filter(function () { return true });
            
            if (allowTimes.length > 0) {
                $(inputId).prop( "disabled", false );
                $(inputId).datetimepicker('reset');
                $(inputId).datetimepicker({
                    disabledHours: disabledHours,
                    allowTimes: allowTimes,
                    format: 'H:i',
                    datepicker:false,
                    step: 60
                });
            } else {
                toastr.error('{{ __("notify.error") }}', '{{ __("notify.Time is not available") }}');
                $(inputId).prop( "disabled", true );
            }
        }

        function setDeleteFormAction() {
            let action = '/msm/appointments-delete/uuid/'+ window.appointmentUUID;
            $('#delete-form').attr('action', action).submit();
        }

        function setUpdateFormAction() {
            let action = '/msm/appointments-update/uuid/'+ window.appointmentUUID;
            $('#update-form .start_date').val($('#update_start_date').val());
            $('#update-form .start_time').val($('#update_start_time').val());
            $('#update-form').attr('action', action).submit();
        }

        function removeItemOnce(arr, value) {
            var index = arr.indexOf(value);
            if (index > -1) {
                arr.splice(index, 1);
            }
            return arr;
        }

        function n(num, len = 2) {
            return `${num}`.padStart(len, '0');
        }

        function goToStep(step) {
            $('.steps').hide();
            $(step).slideDown();
        }
    </script>
    @if($errors->any()) 
        <script type="text/javascript">
            @foreach ($errors->all() as $error)
                toastr.error('{{ __('general.'.$error ) }}');
            @endforeach
        </script> 
    @endif
    @php($message = session()->get('message'))
    @if(isset($message)) 
        <script type="text/javascript">
            toastr.success('{{ __('general.'. $message) }}');
        </script> 
    @endif

</body>

</html>
