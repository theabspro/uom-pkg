@if(config('uom-pkg.DEV'))
    <?php $uom_pkg_prefix = '/packages/abs/uom-pkg/src';?>
@else
    <?php $uom_pkg_prefix = '';?>
@endif

<script type="text/javascript">
	app.config(['$routeProvider', function($routeProvider) {

	    $routeProvider.
	    //Uom
	    when('/uom-pkg/uom/list', {
	        template: '<uom-list></uom-list>',
	        title: 'UOMs',
	    }).
	    when('/uom-pkg/uom/add', {
	        template: '<uom-form></uom-form>',
	        title: 'Add UOM',
	    }).
	    when('/uom-pkg/uom/edit/:id', {
	        template: '<uom-form></uom-form>',
	        title: 'Edit UOM',
	    }).
	    when('/uom-pkg/uom/card-list', {
	        template: '<uom-card-list></uom-card-list>',
	        title: 'UOM Card List',
	    });
	}]);

	//Uoms
    var uom_list_template_url = "{{asset($uom_pkg_prefix.'/public/themes/'.$theme.'/uom-pkg/uom/list.html')}}";
    var uom_form_template_url = "{{asset($uom_pkg_prefix.'/public/themes/'.$theme.'/uom-pkg/uom/form.html')}}";
    var uom_card_list_template_url = "{{asset($uom_pkg_prefix.'/public/themes/'.$theme.'/uom-pkg/uom/card-list.html')}}";
    var uom_modal_form_template_url = "{{asset($uom_pkg_prefix.'/public/themes/'.$theme.'/uom-pkg/partials/uom-modal-form.html')}}";
</script>
<!-- <script type="text/javascript" src="{{asset($uom_pkg_prefix.'/public/themes/'.$theme.'/uom-pkg/uom/controller.js')}}"></script> -->
