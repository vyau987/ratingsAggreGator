<div class="container">
    <div class="row settings">
        <h2>Settings</h2>
        <div class="col-sm-6">
            <div class="row">
                <div class="form-group">
                    <div class="col-sm-12">
                        <h3>Personal details</h3>
                        <div class="input-group">
                            <span class="input-group-addon">Name</span>
                            <input type="text" name="name" class="form-control" placeholder="name" value="{{{ Auth::user()->first_name }}}">
                        </div>
                        <br />

                        <div class="input-group">
                            <span class="input-group-addon">Surname</span>
                            <input type="text" name="surname" class="form-control" placeholder="surname" value="{{{ Auth::user()->surname }}}">
                        </div>
                        <br />
                        
                        <div class="input-group">
                            <span class="input-group-addon">Username</span>
                            <input type="text" name="username" class="form-control" placeholder="username" value="{{{ Auth::user()->username }}}">
                        </div>
                        <br />
                        
                        <div class="input-group">
                            <span class="input-group-addon">Email</i></span>
                            <input type="text" name="email" class="form-control" placeholder="email address" value="{{{ Auth::user()->email }}}">
                        </div>
                        <br />
                        
                        <div class="input-group">
                            Would you like enable automatic Twitter integration?
                            <select class="twitter">
                                <option value="true" {{ Auth::user()->tweets_enabled ? 'selected' : '' }}>Yes</option>
                                <option value="false" {{ Auth::user()->tweets_enabled ? '' : 'selected' }}>No</option>
                            </select>
                        </div>
                        <br />
                        <h3>Interests</h3>
                        <div class="input-group">
                            <span class="input-group-addon">Favourite team football:</i></span>
                            <select class="favourite-team form-control">
                                <option class="favourite-team" value="-1">- select a team -</option>
                                @foreach( Team::all() as $team )
                                    <option class="favourite-team" value="{{ $team->id }}"
                                        @if ($team->id == $user->favourite_team )
                                            selected
                                        @endif
                                        >{{{ $team->name }}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <button class="btn btn-warning" id="edit-button"><i class="fa fa-pencil" id="edit-icon"></i> Edit</button>
            <div class="row">
                <div class="col-sm-12 save-changes">
                    <h3>Save these changes</h3>
                    <p>These settings are important, please enter your password so we know it's you.</p>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <input type="password" name="passcheck" class="form-control" placeholder="password">
                    </div>
                </div>
            </div>
        </div>
            
    </div>
</div>

@section('js')
    @parent
    <script>
    $(function(){
        // disable form inputs on load
        $('.settings').find('input').attr('disabled', true);
        $('.settings').find('select').attr('disabled', true);
        $('.save-changes').hide();
        // hide response message on load
        $('#response-message').hide();

        // hide the response message when user clicks close button.
        $('.alert .close').on('click', function(e) {
            $(this).parent().hide();
        });

        // function to show response message.
        function showErrorMessage(error){
            var $responseMessage = $('#response-message')
                .removeClass()
                .addClass('alert alert-dismissible alert-danger');
            $('#message-type').text('Error: ');
            var message = "";
            for (var key in error) {
                message += ("<p>" + error[key] + "</p>");
            };
            $('#message-text').html(message);
            $responseMessage.show();
        }

        function showSuccessMessage(message) {
            var $responseMessage = $('#response-message')
                .removeClass()
                .addClass('alert alert-dismissible alert-success');
            $('#message-type').text('Success: ');
            $('#message-text').html(message);
            $('#response-message').show();
        }

        // when edit button is clicked, make user settings fields editable and change button
        $('#edit-button').click(function() {
            // if we are saving new information, treat as save button and do ajax stuff
            if($('#edit-button').hasClass('saving')){
                $.ajax({
                    type: "PUT",
                    url: decodeURI("{{ URL::route('users.update', $user->id) }}"),
                    data: {
                        first_name: $('input[name="name"]').val(),
                        surname: $('input[name="surname"]').val(),
                        username: $('input[name="username"]').val(),
                        email_address: $('input[name="email"]').val(),
                        password: $('input[name="passcheck"]').val(),
                        favourite_team: $('.favourite-team option:selected').val()
                    },
                    success: function(json){
                        // display success message and update form values.
                        var message = "Your settings have been saved!";
                        showSuccessMessage(message);
                        // set inputs in settings form to contain new infomration on success.
                        $('input[name="name"]').val(json['first_name']);
                        $('input[name="surname"]').val(json['surname']);
                        $('input[name="username"]').val(json['username']);
                        $('input[name="email"]').val(json['email_address']);
                        $('input[name="passcheck"]').val("");
                        // remove the selected property from the previous item
                        $('.favourite-team option').prop('selected', false);
                        // then add the selected property to the users new favourite team
                        $('.favourite-team option[value="'+json['favourite_team']+'"]').prop('selected', true);
                        // update information in page elements to new information on success
                        $('.user-username').text(" "+json['username']);
                        $('.user-country').text(" "+json['country_code']);
                        $('.user-name').text(" "+json['first_name']+" "+json['surname']);
                        $('.user-email').html(' <a href="mailto:"'+json["email_address"]+'">'+json["email_address"]+'</a>');
                        $('input:not(input[name="query"])').attr('disabled', true);
                        $(this).removeClass()
                               .addClass('btn btn-warning pull-right')
                               .html('<i class="fa fa-pencil"></i> Edit');
                    },
                    error: function(e){
                        // display error message.
                        var responseText = $.parseJSON(e.responseText);
                        showErrorMessage(responseText);
                        $('body').scrollTop(0);
                    }
                }); // end of ajax request
            } else {
                // if we are not saving new information, treat as edit button
                $('.save-changes').show();
                $('input:not(input[name="search-box"])').attr('disabled', false);
                $('.settings').find('select').attr('disabled', false);
                $('#edit-icon').removeClass().addClass('fa fa-save');
                $(this).removeClass()
                       .addClass('btn btn-primary pull-right saving')
                       .html('<i class="fa fa-save"></i> Save');
            }
        });
    });
    </script>
@stop