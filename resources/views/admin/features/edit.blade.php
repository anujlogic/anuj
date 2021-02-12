@extends('backend.layouts.app')

@section('title', 'Edit Feature')

@push('styles')

    
@endpush


@section('content')

    <div class="block-header">
        <a href="{{route('admin.features.index')}}" class="waves-effect waves-light btn btn-danger right m-b-15">
            <i class="material-icons left">arrow_back</i>
            <span>BACK</span>
        </a>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-indigo">
                    <h2>EDIT FEATURE</h2>
                </div>
                <div class="body">
                    <form action="{{route('admin.features.update',$feature->id)}}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" name="name" class="form-control" value="{{$feature->name}}">
                                <label class="form-label">Feature</label>
                            </div>
							<div class="form-line">
                                <select name="feature_type" class="form-control show-tick">
									<option value="">-- Select Type --</option>
									<option value="amenity" @if(old('feature_type') == 'amenity') selected @elseif($feature->feature_type == 'amenity') selected @endif >Amenity</option>
									<option value="furnishing"  @if(old('feature_type') == 'furnishing') selected @elseif($feature->feature_type == 'furnishing') selected @endif >Furnishing</option>
								</select>
                                
                            </div>
                        </div>

                        <button type="submit" class="btn btn-indigo btn-lg m-t-15 waves-effect">
                            <i class="material-icons">update</i>
                            <span>Update</span>
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('scripts')



@endpush
