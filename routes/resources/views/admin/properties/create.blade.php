@extends('backend.layouts.app')

@section('title', 'Create Property')

@push('styles')

    <link rel="stylesheet" href="{{asset('admin/backend/plugins/bootstrap-select/css/bootstrap-select.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />

@endpush


@section('content')

    <div class="block-header"></div>

    <div class="row clearfix">
        <form action="{{route('admin.properties.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="col-lg-8 col-md-4 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-indigo">
                    <h2>CREATE PROPERTY</h2>
                </div>
                <div class="body">
					
					<div class="form-group form-float">
                        <div class="form-line">
							<label>You Are</label>
                            <select name="seller_type" class="form-control show-tick">
                                <option value="">-- Please select --</option>
                                <option value="owner" @if(old('seller_type') == 'owner') selected @endif >Owner</option>
                                <option value="dealer" @if(old('seller_type') == 'dealer') selected @endif>Dealer</option>
								<option value="builder" @if(old('seller_type') == 'builder') selected @endif>Builder</option>
                            </select>
                        </div>
                    </div>
					
					 <div class="form-group form-float">
                        <div class="form-line ">
                            <label>Select Purpose</label>
                            <select name="purpose" class="form-control show-tick">
                                <option value="">-- Please select --</option>
                                <option value="sale" @if(old('purpose') == 'sale') selected @endif>Sale</option>
                                <option value="rent" @if(old('purpose') == 'rent') selected @endif>Rent</option>
                            </select>
                        </div>
                    </div>
					
					<div class="form-group form-float">
                        <div class="form-line">
							<label>Property Type</label>
                            <select name="residential_property_type" class="form-control show-tick" onChange="updatePropertyType(this.value);">
                                <option value="">-- Please select --</option>
                                <option value="apartment_flat" @if(old('residential_property_type') == 'apartment_flat') selected @endif>Apartment/Flat</option>
                                <option value="house_villa" @if(old('residential_property_type') == 'house_villa') selected @endif>House/Villa</option>
							</select>
                        </div>
                    </div>
					
					<div class="form-group form-float" id="residential_apartment_div" style="display:none;">
                        <div class="form-line">
							<label>Apartment/Flat Type</label>
                            <select name="residential_apartment_type" class="form-control show-tick">
                                <option value="">-- Please select --</option>
                                <option value="residential_apartment" @if(old('residential_apartment_type') == 'residential_apartment') selected @endif>Residential Apartment</option>
                                <option value="independent_floor" @if(old('residential_apartment_type') == 'independent_floor') selected @endif>Independent Floor</option>
								<option value="studio_apartment" @if(old('residential_apartment_type') == 'studio_apartment') selected @endif>Studio Apartment</option>
								<option value="serviced_apartment" @if(old('residential_apartment_type') == 'serviced_apartment') selected @endif>Serviced Apartment</option>
							</select>
                        </div>
                    </div>
					
					<div class="form-group form-float" id="residential_house_div" style="display:none;">
                        <div class="form-line">
							<label>House/Villa Type</label>
                            <select name="residential_house_type" class="form-control show-tick">
                                <option value="">-- Please select --</option>
                                <option value="independent_house" @if(old('residential_house_type') == 'independent_house') selected @endif>Independent House/Villa</option>
                                <option value="farm_house" @if(old('residential_house_type') == 'farm_house') selected @endif>Farm House</option>
							</select>
                        </div>
                    </div>
					
                    <div class="form-group form-float">
                        <div class="form-line">
                            <input type="text" name="title" class="form-control" value="{{old('title')}}">
                            <label class="form-label">Property Title</label>
                        </div>
                    </div>

                    <div class="form-group form-float">
                        <div class="form-line">
                            <input type="number" class="form-control" name="price" required>
                            <label class="form-label">Price</label>
                        </div>
                    </div>

                    <div class="form-group form-float">
                        <div class="form-line">
                            <input type="number" class="form-control" name="bedroom" required>
                            <label class="form-label">Bedroom</label>
                        </div>
                    </div>

                    <div class="form-group form-float">
                        <div class="form-line">
                            <input type="number" class="form-control" name="bathroom" required>
                            <label class="form-label">Bathroom</label>
                        </div>
                    </div>
					
					<div class="form-group form-float">
                        <div class="form-line">
                            <button class="btn waves-effect waves-light btn-large indigo darken-4" type="button" onClick="getLocation();">
								Detect Location
							</button>
                        </div>
                    </div>
					
					<div class="form-group form-float">
                        <div class="form-line">
							<label>Select State</label>
							<select name="state_id" id="state_id" class="form-control show-tick"  onChange="getCities(this.value);">
								<option value="">-- Please select --</option>
								@foreach($states as $state)
									<option value="{{ $state->id }}">{{ $state->state_name }}</option>
								@endforeach
							</select>
                        </div>
                    </div>
					
					<div class="form-group form-float">
                        <div class="form-line">
							<label>Select City</label>
							<select name="city_id" id="city_id" class="form-control show-tick" >
								<option value="">-- Please select --</option>
							</select>
                        </div>
                    </div>
					
					{{--<div class="form-group form-float">
                        <div class="form-line">
                            <input type="text" class="form-control" name="city" required>
                            <label class="form-label">City</label>
                        </div>
                    </div> --}}

                    <div class="form-group form-float">
                        <div class="form-line">
                            <input type="text" class="form-control" name="address" id="address" value="" required>
                            <label class="form-label">Address</label>
                        </div>
                    </div>

                    <div class="form-group form-float">
                        <div class="form-line">
                            <input type="number" class="form-control" name="area" required>
                            <label class="form-label">Area</label>
                        </div>
                        <div class="help-info">Square Feet</div>
                    </div>

                    <div class="form-group">
                        <input type="checkbox" id="featured" name="featured" class="filled-in" value="1" />
                        <label for="featured">Featured</label>
                    </div>

                    <hr>
                    <div class="form-group">
                        <label for="tinymce">Description</label>
                        <textarea name="description" id="tinymce">{{old('description')}}</textarea>
                    </div>

                    {{--<hr>
                    <div class="form-group">
                        <label for="tinymce-nearby">Nearby</label>
                        <textarea name="nearby" id="tinymce-nearby">{{old('nearby')}}</textarea>
                    </div> --}}

                </div>
            </div>
            <div class="card">
                <div class="header">
                    <h2>GALLERY IMAGE</h2>
                </div>
                <div class="body">
                    <input id="input-id" type="file" name="gallaryimage[]" class="file" data-preview-file-type="text" multiple>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-indigo">
                    <h2>SELECT</h2>
                </div>
                <div class="body">

                    {{--<div class="form-group form-float">
                        <div class="form-line {{$errors->has('purpose') ? 'focused error' : ''}}">
                            <label>Select Purpose</label>
                            <select name="purpose" class="form-control show-tick">
                                <option value="">-- Please select --</option>
                                <option value="sale">Sale</option>
                                <option value="rent">Rent</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group form-float">
                        <div class="form-line {{$errors->has('type') ? 'focused error' : ''}}">
                            <label>Select type</label>
                            <select name="type" class="form-control show-tick">
                                <option value="">-- Please select --</option>
                                <option value="house">House</option>
                                <option value="apartment">Apartment</option>
                            </select>
                        </div>
                    </div> --}}

                    <h5>Amenities</h5>
                    <div class="form-group demo-checkbox">
                        @foreach($features as $feature)
							@if($feature->feature_type == 'amenity')
								<input type="checkbox" id="features-{{$feature->id}}" name="features[]" class="filled-in chk-col-indigo" value="{{$feature->id}}" />
								<label for="features-{{$feature->id}}">{{$feature->name}}</label>
							@endif
                        @endforeach
                    </div>
					
					<h5>Furnishings</h5>
                    <div class="form-group demo-checkbox">
                        @foreach($features as $feature)
							@if($feature->feature_type == 'furnishing')
								<input type="checkbox" id="features-{{$feature->id}}" name="features[]" class="filled-in chk-col-indigo" value="{{$feature->id}}" />
								<label for="features-{{$feature->id}}">{{$feature->name}}</label>
							@endif
                        @endforeach
                    </div>

                    <div class="form-group form-float">
                        <div class="form-line">
                            <input type="text" class="form-control" name="video">
                            <label class="form-label">Video</label>
                        </div>
                        <div class="help-info">Youtube Link</div>
                    </div>

                    {{--<div class="clearfix">
                        <h5>Google Map</h5>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" name="location_latitude" class="form-control" required/>
                                <label class="form-label">Latitude</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" name="location_longitude" class="form-control" required/>
                                <label class="form-label">Longitude</label>
                            </div>
                        </div>
                    </div> --}}
                    
                </div>
            </div>
            <div class="card">
                <div class="header bg-indigo">
                    <h2>FLOOR PLAN</h2>
                </div>
                <div class="body">
                    <div class="form-group">
                        <input type="file" name="floor_plan">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="header bg-indigo">
                    <h2>FEATURED IMAGE</h2>
                </div>
                <div class="body">
                    <div class="form-group">
                        <input type="file" name="image">
                    </div>

                    {{-- BUTTON --}}
                    <a href="{{route('admin.properties.index')}}" class="btn btn-danger btn-lg m-t-15 waves-effect">
                        <i class="material-icons left">arrow_back</i>
                        <span>BACK</span>
                    </a>

                    <button type="submit" class="btn btn-indigo btn-lg m-t-15 waves-effect">
                        <i class="material-icons">save</i>
                        <span>SAVE</span>
                    </button>
                </div>
            </div>
        </div>
		<input type="hidden" name="location_latitude" id="location_latitude" value=""/>
		<input type="hidden" name="location_longitude" id="location_longitude" value=""/>
        </form>
    </div>

@endsection


@push('scripts')
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/js/fileinput.min.js"></script>

    {{--<script src="{{ asset('admin/backend/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script> --}}
    <script src="{{asset('admin/backend/plugins/tinymce/tinymce.js')}}"></script>
    <script>
        $(function () {
            $("#input-id").fileinput();
			//$('#amenities,#furnishing,#seller_type,#purpose,#residential_property_type,#residential_apartment_type,#residential_house_type').formSelect();
			updatePropertyType = function(property_type){
				if(property_type == 'apartment_flat'){
					$("#residential_apartment_div").slideDown("slow");
					$("#residential_house_div").slideUp("slow");
				}else{
					$("#residential_apartment_div").slideUp("slow");
					$("#residential_house_div").slideDown("slow");
				}
			}
			
        });

        $(function () {
            tinymce.init({
                selector: "textarea#tinymce",
                theme: "modern",
                height: 300,
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste textcolor colorpicker textpattern imagetools'
                ],
                toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                toolbar2: 'print preview media | forecolor backcolor emoticons',
                image_advtab: true
            });
            tinymce.suffix = ".min";
            tinyMCE.baseURL = '{{asset('public/backend/plugins/tinymce')}}';
        });

        $(function () {
            tinymce.init({
                selector: "textarea#tinymce-nearby",
                theme: "modern",
                height: 300,
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste textcolor colorpicker textpattern imagetools'
                ],
                toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                toolbar2: '',
                image_advtab: true
            });
            tinymce.suffix = ".min";
            tinyMCE.baseURL = '{{asset('public/backend/plugins/tinymce')}}';
        });
		
	function getLocation() {
		var error_msg = '';
		
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(getLocationAddress);
		}else{
			error_msg = "Geolocation is not supported by this browser.";
		}
	}

	function getLocationAddress(position) {
		
		var url = "{{action('AjaxController@getLocationAddress')}}";
		//alert('_token={{csrf_token()}}&lat='+position.coords.latitude+"&lng="+position.coords.longitude);return;
		$.ajax({
			type:'POST',
			url:url,
			data:'_token={{csrf_token()}}&lat='+position.coords.latitude+"&lng="+position.coords.longitude,
			success:function(data) {//alert(data.latitude);alert(data);
				//$("#city_id").val(data.city_id);
				$("#city_id").html(data.city_options_str);
				$("#state_id").val(data.state_id);
				$("#address").val(data.street_address);
				$("#location_latitude").val(data.latitude);
				$("#location_longitude").val(data.longitude);
			}
		});
	}
	
	function getCities(state_id) {
		var url = "{{action('AjaxController@getStateCities')}}";
		
		$.ajax({
			type:'POST',
			url:url,
			data:'_token={{csrf_token()}}&state_id='+state_id,
			success:function(data) {//alert(data.options_str);
				$("#city_id").html(data.options_str);
			}
		});
    }
    </script>

@endpush
