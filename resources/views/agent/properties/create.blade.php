@extends('frontend.layouts.app')

@section('styles')

@endsection

@section('content')

    <section class="section">
        <div class="container">
            <div class="row">

                <div class="col s12 m3">
                    <div class="agent-sidebar">
                        @include('agent.sidebar')
                    </div>
                </div>

                <div class="col s12 m9">
                    <div class="agent-content">
                        <h4 class="agent-title">CREATE PROPERTY</h4>

                        <form action="{{route('agent.properties.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
							
							<div class="row">
								<div class="input-field col s6">
									<select  name="seller_type" id="seller_type">
										<option value="">-- Please select --</option>
										<option value="owner" @if(old('seller_type') == 'owner') selected @endif >Owner</option>
										<option value="dealer" @if(old('seller_type') == 'dealer') selected @endif>Dealer</option>
										<option value="builder" @if(old('seller_type') == 'builder') selected @endif>Builder</option>
									</select>
									<label class="label-custom">You Are</label>
								</div>
								
								<div class="input-field col s6">
									<select  name="purpose" id="purpose">
										<option value="">-- Please select --</option>
										<option value="sale" @if(old('purpose') == 'sale') selected @endif>Sale</option>
										<option value="rent" @if(old('purpose') == 'rent') selected @endif>Rent</option>
									</select>
									<label class="label-custom">Select Purpose</label>
								</div>
							</div>
							
							<div class="row">
								<div class="input-field col s12" >
									<select  name="residential_property_type" id="residential_property_type" onChange="updatePropertyType(this.value);">
										<option value="">-- Please select --</option>
										<option value="apartment_flat" @if(old('residential_property_type') == 'apartment_flat') selected @endif>Apartment/Flat</option>
										<option value="house_villa" @if(old('residential_property_type') == 'house_villa') selected @endif>House/Villa</option>
									</select>
									<label class="label-custom">Property Type</label>
								</div>
							</div>
							
							<div class="row" id="residential_apartment_div" style="display:none;">
								<div class="input-field col s12" >
									<select  name="residential_apartment_type" id="residential_apartment_type">
										<option value="">-- Please select --</option>
										<option value="residential_apartment" @if(old('residential_apartment_type') == 'residential_apartment') selected @endif>Residential Apartment</option>
										<option value="independent_floor" @if(old('residential_apartment_type') == 'independent_floor') selected @endif>Independent Floor</option>
										<option value="studio_apartment" @if(old('residential_apartment_type') == 'studio_apartment') selected @endif>Studio Apartment</option>
										<option value="serviced_apartment" @if(old('residential_apartment_type') == 'serviced_apartment') selected @endif>Serviced Apartment</option>
									</select>
									<label class="label-custom">Apartment/Flat Type</label>
								</div>
							</div>
							
							<div class="row" id="residential_house_div" style="display:none;">
								<div class="input-field col s12" >
									<select name="residential_house_type" id="residential_house_type">
										<option value="">-- Please select --</option>
										<option value="independent_house" @if(old('residential_house_type') == 'independent_house') selected @endif>Independent House/Villa</option>
										<option value="farm_house" @if(old('residential_house_type') == 'farm_house') selected @endif>Farm House</option>
									</select>
									<label class="label-custom">House/Villa Type</label>
								</div>
							</div>
								
                            <div class="row">
                                <div class="input-field col s12">
                                    <i class="material-icons prefix">title</i>
                                    <input id="title" name="title" type="text" class="validate" data-length="200" value="{{old('title')}}">
                                    <label for="title">Title</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <i class="material-icons prefix">monetization_on</i>
                                    <input id="price" name="price" type="number" class="validate" value="{{old('price')}}">
                                    <label for="price">Price</label>
                                </div>
                                <div class="input-field col s6">
                                    <i class="material-icons prefix">business</i>
                                    <input id="area" name="area" type="number" class="validate" value="{{old('area')}}">
                                    <label for="area">Floor Area</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s6">
                                    <i class="material-icons prefix">airline_seat_flat</i>
                                    <input id="bedroom" name="bedroom" type="number" class="validate" value="{{old('bedroom')}}">
                                    <label for="bedroom">Bedroom</label>
                                </div>
                                <div class="input-field col s6">
                                    <i class="material-icons prefix">event_seat</i>
                                    <input id="bathroom" name="bathroom" type="number" class="validate" value="{{old('bathroom')}}">
                                    <label for="bathroom">Bathroom</label>
                                </div>
                            </div>
                            <div class="row">
								<div class="input-field col s4">
									<button class="btn waves-effect waves-light btn-large indigo darken-4" type="button" onClick="getLocation();">
                                        Detect Location
                                        <i class="material-icons right">location_city</i>
                                    </button>
								 </div>
                                <div class="input-field col s4">
                                   {{-- <i class="material-icons prefix">location_city</i>--}}
										<select name="state_id" id="state_id" onChange="getCities(this.value);">
											<option value="">-- Select State --</option>
											@foreach($states as $state)
												<option value="{{ $state->id }}">{{ $state->state_name }}</option>
											@endforeach
										</select>
                                    {{--<label for="state">State</label>--}}
                                </div>
								
								 <div class="input-field col s4">
								 {{--<i class="material-icons prefix">location_city</i> --}}
                                    <select name="city_id" id="city_id">
										<option value="">-- Select City --</option>
									</select>
                                   {{-- <label for="city">City</label>--}}
                                </div>
								
                            </div>
							
							<div class="row">
								<div class="input-field col s12">
                                    <i class="material-icons prefix">account_balance</i>
                                    <textarea id="address" name="address" class="materialize-textarea">{{old('address')}}</textarea>
                                    <label for="address">Address</label>
                                </div>
							</div>
							
                            <div class="row">
                                <div class="input-field col s3">
                                    <p>
                                        <label>
                                            <input type="checkbox" value="1" name="featured" class="filled-in" @if(old('featured') == 1) checked="checked" @endif />
                                            <span>Featured</span>
                                        </label>
                                    </p>
                                </div>
                                <div class="input-field col s9">
                                    <i class="material-icons prefix">mode_edit</i>
                                    <textarea id="description" name="description" class="materialize-textarea">{{old('description')}}</textarea>
                                    <label for="description">Description</label>
                                </div>
                            </div>

                            <div class="row">
							{{--<div class="col s3">
                                    <label class="label-custom" for="type">Property Type</label>
                                    <p>
                                        <label>
                                            <input class="with-gap" name="type" value="house" type="radio" @if(old('type') == 'house') checked="checked" @endif />
                                            <span>Sale</span>
                                        </label>
                                    <p>
                                    </p>
                                        <label>
                                            <input class="with-gap" name="type" value="apartment" type="radio" @if(old('type') == 'apartment') checked="checked" @endif />
                                            <span>Rent</span>
                                        </label>
                                    </p>
                                </div>
                                <div class="col s3">
                                    <label class="label-custom" for="purpose">Property Purpose</label>
                                    <p>
                                        <label>
                                            <input class="with-gap" name="purpose" value="sale" type="radio" @if(old('purpose') == 'sale') checked="checked" @endif  />
                                            <span>House</span>
                                        </label>
                                    <p>
                                    </p>
                                        <label>
                                            <input class="with-gap" name="purpose" value="rent" type="radio" @if(old('purpose') == 'rent') checked="checked" @endif />
                                            <span>Apartment</span>
                                        </label>
                                    </p>
                                </div> --}}
                                <div class="input-field col s6">
                                    <select multiple name="features[]" id="amenities">
                                        @foreach($features as $feature)
											@if($feature->feature_type == 'amenity')
												<option value="{{ $feature->id }}">{{ $feature->name }}</option>
											@endif
                                        @endforeach
                                    </select>
                                    <label class="label-custom">Select Amenities</label>
                                </div>
								
								 <div class="input-field col s6">
                                    <select multiple name="features[]" id="furnishing">
                                        @foreach($features as $feature)
											@if($feature->feature_type == 'furnishing')
												<option value="{{ $feature->id }}">{{ $feature->name }}</option>
											@endif
                                        @endforeach
                                    </select>
                                    <label class="label-custom">Select Furnishings</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="file-field input-field col s12">
                                    <div class="btn indigo">
                                        <span>Featured Image</span>
                                        <input type="file" name="image">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate" type="text">
                                    </div>
                                </div>
                            </div>

							{{-- <div class="row">
                                <div class="input-field col s6">
                                    <i class="material-icons prefix">map</i>
                                    <input id="location_latitude" name="location_latitude" type="text" class="validate" value="{{old('latitude')}}">
                                    <label for="location_latitude">Latitude</label>
                                </div>
                                <div class="input-field col s6">
                                    <i class="material-icons prefix">map</i>
                                    <input id="location_longitude" name="location_longitude" type="text" class="validate" value="{{old('longitude')}}">
                                    <label for="location_longitude">Longitude</label>
                                </div>
                            </div>--}}

                            <div class="row">
                                <div class="input-field col s12">
                                    <i class="material-icons prefix">voice_chat</i>
                                    <input id="video" name="video" type="text" class="validate" value="{{old('video')}}">
                                    <label for="video">Youtube Link</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="file-field input-field col s12">
                                    <div class="btn indigo">
                                        <span>Floor Plan</span>
                                        <input type="file" name="floor_plan">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate" type="text">
                                    </div>
                                </div>
                            </div>

                            {{--<div class="row">
                                <div class="input-field col s12">
                                    <i class="material-icons prefix">place</i>
                                    <textarea id="nearby" name="nearby" class="materialize-textarea">{{old('nearby')}}</textarea>
                                    <label for="nearby">Nearby</label>
                                </div>
                            </div> --}}

                            <div class="row">
                                <div class="file-field input-field col s12">
                                    <div class="btn indigo">
                                        <span>Upload Gallery Images</span>
                                        <input type="file" name="gallaryimage[]" multiple>
                                        <span class="helper-text" data-error="wrong" data-success="right">Upload one or more images</span>
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate" type="text" placeholder="Upload one or more images">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col s12 m-t-30">
                                    <button class="btn waves-effect waves-light btn-large indigo darken-4" type="submit">
                                        Submit
                                        <i class="material-icons right">send</i>
                                    </button>
                                </div>
                            </div>
							<input type="hidden" name="location_latitude" id="location_latitude" value=""/>
							<input type="hidden" name="location_longitude" id="location_longitude" value=""/>
                        </form> 


                    </div>
                </div> <!-- /.col -->

            </div>
        </div>
    </section>

@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('input#title, textarea#nearby').characterCounter();
       $('#amenities,#furnishing,#seller_type,#purpose,#residential_property_type,#residential_apartment_type,#residential_house_type').formSelect();
		
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
	
	function getLocation() {
		var error_msg = '';
		
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(getLocationAddress);
		}else{
			error_msg = "Geolocation is not supported by this browser.";
		}
	}

	function getLocationAddress(position) {
		//alert( "Latitude: " + position.coords.latitude +"<br>Longitude: " + position.coords.longitude);
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
@endsection

<style>
#state_id,#city_id{display:block;}
</style>