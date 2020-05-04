@if(config('uom-pkg.DEV'))
    <?php $uom_pkg_prefix = '/packages/abs/uom-pkg/src';?>
@else
    <?php $uom_pkg_prefix = '';?>
@endif

<script type="text/javascript">
    var uoms_voucher_list_template_url = "{{asset($uom_pkg_prefix.'/public/themes/'.$theme.'/uom-pkg/uom/uom.html')}}";
</script>
<script type="text/javascript" src="{{asset($uom_pkg_prefix.'/public/themes/'.$theme.'/uom-pkg/uom/controller.js')}}"></script>
