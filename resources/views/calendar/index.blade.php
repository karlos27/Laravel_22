<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Calendar') }}
        </h2>
    </x-slot>

    <!-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Here is your calendar.
                </div>
            </div>
        </div>
    </div> -->

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">
                <div class="row">
                    <div class="col-6">
                        <div id="calendar">
                        </div>
                    </div>
                    <div class="col-6">
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Booking Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <!-- <label for="InputEvent">New Event</label> -->
            <input type="text" class="form-control event" id="InputEvent" placeholder="Enter an event">
            <span id="titleError" class="text-danger"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
        <button type="button" id="createEvent" class="btn btn-outline-success">Save changes</button>
      </div>
    </div>
  </div>
</div>

<!-- Calendar -->
<script>
    $(document).ready(function(){

        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var booking = @json($events);
        
        $('#calendar').fullCalendar({
            events: booking,
            selectable: true,
            selectHelper: true,
            select: function(start, end, allDays){
                $('#bookingModal').modal('toggle');

                $('#createEvent').click(function(){
                    var title       = $('#InputEvent').val();
                    var start_date  = moment(start).format('YYYY-MM-DD');
                    var end_date    = moment(end).format('YYYY-MM-DD');

                    $.ajax({
                        url:"{{ route('calendar.store') }}",
                        type:"POST",
                        dataType:'json',
                        data:{ title, start_date, end_date },
                        success: function(response)
                        {
                            $('#bookingModal').modal('hide');
                            $('#calendar').fullCalendar('renderEvent', {
                                'title' : response.title,
                                'start' : response.start_date,
                                'end'   : response.end_date,
                            })
                        },
                        error: function(error)
                        {
                            if(error.responseJSON.errors){
                                $('#titleError').html(error.responseJSON.errors.title);
                            }
                        },
                    });
                });
            },
            editable:true,
            eventDrop: function(event) {
                var id          = event.id;
                var start_date  = moment(event.start).format('YYYY-MM-DD');
                var end_date    = moment(event.end).format('YYYY-MM-DD');

                $.ajax({
                        url:"{{ route('calendar.update', '') }}" + '/' + id,
                        type:"PATCH",
                        dataType:'json',
                        data:{ start_date, end_date },
                        success: function(response)
                        {
                            Swal.fire({
                                title: 'Event Updated!',
                                icon: 'success',
                                iconColor: '#009000',
                                timer: 3000,
                                toast: true,
                                position: 'bottom-right',
                                timerProgressBar: true,
                                showConfirmButton: false,
                            });
                        },
                        error: function(error)
                        {
                            //console.log(error)
                        },
                });
            },
            eventClick: function(event){
                //console.log(event)
                var id = event.id;

                $.ajax({
                        url:"{{ route('calendar.delete', '') }}" + '/' + id,
                        type:"DELETE",
                        dataType:'json',
                        success: function(response)
                        {
                            $('#calendar').fullCalendar('removeEvents', response);
                            if(confirm('{{ Auth::user()->name }}, are you sure?')){
                                Swal.fire({
                                    title: 'Event Deleted!',
                                    icon: 'success',
                                    iconColor: '#009000',
                                    timer: 3000,
                                    toast: true,
                                    position: 'bottom-right',
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                });
                            }
                        },
                        error: function(error)
                        {
                            //console.log(error)
                        },
                });

            }

            
        })
    });
</script>

