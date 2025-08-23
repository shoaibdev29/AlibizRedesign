@extends('layouts.blank')

@section('content')
    <div class="container">
        <div class="row mt-5">
            <div class="col-2"></div>
            <div class="col-md-8">
                <div class="mar-ver pad-btm text-center">
                    <h1 class="h3 text-white">{{ translate('Hexacom Software Update') }}</h1>
                    @if(env('SOFTWARE_VERSION') == 7.3)
                        <div class="alert alert-danger px-1 mt-2" role="alert">
                            {{ translate('Important Notice: We’ve upgraded the Firebase push notification system to a new and improved version as the old one will be phased out by June 2024.
                                Make sure your system is up-to-date to keep getting all the notification seamlessly please do check the Notification settings in the Admin panel.
                                Thanks for staying connected!.')  }}
                            <a class="alert-link" target="_blank" href="https://docs.6amtech.com/docs-hexacom/admin-panel/mandatory-setup#firebase-configuration-for-notification">{{ translate('kindly follow the documentation') }}</a>
                        </div>
                    @endif
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <form method="POST" action="{{route('update-system')}}">
                            @csrf
                            <div class="form-group">
                                <label for="purchase_code" class="gap-2 mb-2">{{ translate('Codecanyon Username') }}</label>
                                <input type="text" class="form-control" id="username" value="{{env('BUYER_USERNAME')}}"
                                       name="username" required>
                            </div>

                            <div class="form-group">
                                <label for="purchase_code" class="gap-2 mb-2 mt-2">{{ translate('Purchase Code') }}</label>
                                <input type="text" class="form-control" id="purchase_key"
                                       value="{{env('PURCHASE_CODE')}}" name="purchase_key" required>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-2"></div>
        </div>
    </div>
@endsection
