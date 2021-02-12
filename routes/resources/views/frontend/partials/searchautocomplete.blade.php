
<!-- SEARCH SECTION -->
<style type="text/css">
.typeahead__query,.typeahead__list {width:50%;} .typeahead__button {margin-left:10px;} .search-btn{height:2.9rem;} .searchbar{height:auto;} .search-btn{cursor:pointer;}
</style>
<section class="indigo darken-2 white-text center">
    <div class="container">
        <div class="row m-b-0">
            <div class="col s12">
			<div style="float:left;"><h4>Property to sale or rent</h4></div>
			<div style="clear:both"></div>
			<div style="float:left;margin-top:5px;margin-bottom:10px;"><b>Search using 'town name', 'postcode' or 'station'</b></div>
			 <div class="searchbar">
			 
               <var id="result-container" class="result-container"></var>
 
				<form id="form-country_v2" name="form-country_v2" action="{{ route('search')}} ">
					<div class="typeahead__container">
						<div class="typeahead__field">
							<div class="typeahead__query">
								<input class="js-typeahead-country_v2" name="loc" placeholder="Search" autocomplete="off" value="{{request('loc')}}"/>
							</div>
							<div class="typeahead__button">
								<input type="submit" name="search" value="Search" class="search-btn">
								<input type="hidden"  id="loc_id" name="loc_id" value="{{request('loc_id')}}"  />	
							</div>
						</div>
					</div>
				</form>
				</div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
var token = '{{csrf_token()}}';
var url = "{{action('AjaxController@searchAutocomplete')}}";//alert(url);
@verbatim

$('.js-typeahead-country_v2').typeahead({
	minLength: 2,
	maxItem:25,
	dynamic: true,
	hint:false,
	highlight: true,
	filter: false,
	display: ['label'],
	
	source: {
		default_source: {
			ajax: {
				method: "POST",
				url: url,
				data: {q: '{{query}}',_token:token},
				cache: false,
			}
		}
	},
	callback: {
		onClickAfter: function (node, a, item, event) {
			$("#loc_id").val(item.id);
		},
		onCancel: function (node, item, event) {
			
		},
		onLayoutBuiltBefore: function (node, query, result, resultHtmlList) {
			
		},
		onSearch (node, query){
			
		}
	}
});

@endverbatim
</script>