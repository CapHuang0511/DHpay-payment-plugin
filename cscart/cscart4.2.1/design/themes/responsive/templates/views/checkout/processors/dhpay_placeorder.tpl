<!DOCTYPE html>
<html lang="{$smarty.const.CART_LANGUAGE}">
<head>
    <title>{__('checkout')}</title>
    {include file="meta.tpl"}
    {include file="common/styles.tpl" include_dropdown=true}
    {include file="common/scripts.tpl"}
</head>

<body>
    <form action="{""|fn_url}" method="post">
        {$smarty.capture.final_section nofilter}
        {include file="buttons/place_order.tpl" but_text=__("submit_my_order") but_name="dispatch[checkout.place_order]" but_id="place_order"}
    </form>
    <div id="place_order_data" class="hidden">
</body>
</html>