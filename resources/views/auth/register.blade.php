@extends('layouts.global')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register a New Account') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-12">
                                <h6>Mandatory Information</h6>
                                <hr>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="firstname" class="col-md-4 col-form-label text-md-right">{{ __('First Name / Given Name') }}*</label>

                            <div class="col-md-6">
                                <input id="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" value="{{ old('firstname') }}" required autocomplete="firstname" autofocus>

                                @error('firstname')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="surname" class="col-md-4 col-form-label text-md-right">{{ __('Surname / Family Name') }}*</label>

                            <div class="col-md-6">
                                <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror" name="surname" value="{{ old('surname') }}" required autocomplete="surname" autofocus>

                                @error('surname')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}*</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}*</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row pb-4">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}*</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <h6>Optional Information</h6>
                                <hr>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="orcid" class="col-md-4 col-form-label text-md-right">{{ __('ORCID') }}</label>

                            <div class="col-md-6">
                                <input id="orcid" type="text" class="form-control @error('orcid') is-invalid @enderror" name="orcid" value="{{ old('orcid') }}">

                                @error('orcid')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="institution_name" class="col-md-4 col-form-label text-md-right">{{ __('Insitution Name') }}</label>

                            <div class="col-md-6">
                                <input id="institution_name" type="text" class="form-control @error('institution_name') is-invalid @enderror" name="institution_name" value="{{ old('institution_name') }}">

                                @error('institution_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="institution_address" class="col-md-4 col-form-label text-md-right">{{ __('Insitution Address') }}</label>

                            <div class="col-md-6">
                                <input id="institution_address" type="text" class="form-control @error('institution_address') is-invalid @enderror" name="institution_address" value="{{ old('institution_address') }}">

                                @error('institution_address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="department_name" class="col-md-4 col-form-label text-md-right">{{ __('Department Name') }}</label>

                            <div class="col-md-6">
                                <input id="department_name" type="text" class="form-control @error('department_name') is-invalid @enderror" name="department_name" value="{{ old('department_name') }}">

                                @error('department_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="linkedin" class="col-md-4 col-form-label text-md-right">{{ __('LinkedIn Profile') }}</label>

                            <div class="col-md-6">
                                <input id="linkedin" type="text" class="form-control @error('linkedin') is-invalid @enderror" name="linkedin" value="{{ old('linkedin') }}">

                                @error('linkedin')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row pb-4">
                            <label for="twitter" class="col-md-4 col-form-label text-md-right">{{ __('Twitter Feed') }}</label>

                            <div class="col-md-6">
                                <input id="twitter" type="text" class="form-control @error('twitter') is-invalid @enderror" name="twitter" value="{{ old('twitter') }}">

                                @error('twitter')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row pb-4">
                            <div class="col-md-12">
                                <h6>Privacy Notification</h6>
                                <hr>
                                <p>This site is designed to facilitate the submission and sharing of scientific figures.
                                    It collects Personally Identifiable Information such as the names, e-mail addresses,
                                    organizational affiliations, IP addresses, and Open Researcher and Contributor IDs (ORC ID)
                                    of those submitting content (the "PI Information").</p>
                                <p>This PI Information is collected to facilitate communications regarding posted figures,
                                    to track/report on submissions and system usage. This PI information may be shared with
                                    the data controller (EMBO) and other registered users. The PI Information stored in relationship
                                    to a figure submission might be used in case of ethics concerns or violations.</p>
                                <p>Please confirm the following:</p>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="confirmation[]" id="confirmation-1" value="true">
                                    <label class="custom-control-label" for="confirmation-1">
                                        I agree with the conditions above and confirm my personal information is correct.</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="confirmation[]" id="confirmation-2" value="true">
                                    <label class="custom-control-label" for="confirmation-2">
                                        I will obtain consent from all persons and entities that may have intellectual property rights
                                        pertaining to the content I will post and share on this platform.</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="confirmation[]" id="confirmation-3" value="true">
                                    <label class="custom-control-label" for="confirmation-3">
                                        I will obtain permission from any relevant co-authors before publicly posting or sharing content.</label>
                                </div>
                                @if ($errors->has('confirmation.*'))
                                <div class="alert alert-danger mb-0 mt-4" role="alert">
                                    You must accept all terms and conditions in order to proceed.
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection