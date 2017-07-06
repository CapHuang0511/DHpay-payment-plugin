<div class="control-group">
    <label class="control-label" for="currency">{__("currency")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][currency]" id="currency">
            <option value="CAD" {if $processor_params.currency == "CAD"}selected="selected"{/if}>{__("currency_code_cad")}</option>
            <option value="EUR" {if $processor_params.currency == "EUR"}selected="selected"{/if}>{__("currency_code_eur")}</option>
            <option value="GBP" {if $processor_params.currency == "GBP"}selected="selected"{/if}>{__("currency_code_gbp")}</option>
            <option value="USD" {if $processor_params.currency == "USD"}selected="selected"{/if}>{__("currency_code_usd")}</option>
            <option value="JPY" {if $processor_params.currency == "JPY"}selected="selected"{/if}>{__("currency_code_jpy")}</option>
            <option value="RUB" {if $processor_params.currency == "RUB"}selected="selected"{/if}>{__("currency_code_rur")}</option>
            <option value="AUD" {if $processor_params.currency == "AUD"}selected="selected"{/if}>{__("currency_code_aud")}</option>
            <option value="SGD" {if $processor_params.currency == "SGD"}selected="selected"{/if}>{__("currency_code_sgd")}</option>
            <option value="SEK" {if $processor_params.currency == "SEK"}selected="selected"{/if}>{__("currency_code_sek")}</option>
            <option value="CNY" {if $processor_params.currency == "CNY"}selected="selected"{/if}>{__("currency_code_cny")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="mode">{__("test_live_mode")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][mode]" id="mode">
            <option value="test" {if $processor_params.mode == "test"}selected="selected"{/if}>{__("test")}</option>
            <option value="live" {if $processor_params.mode == "live"}selected="selected"{/if}>{__("live")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="mode">{__("iframe_mode")}:</label>
    <div class="controls">
        <select name="payment_data[processor_params][iframe_mode]" id="iframe_mode">
            <option value="Y" {if $processor_params.iframe_mode == "Y"}selected="selected"{/if}>{__("enable")}</option>
            <option value="N" {if $processor_params.iframe_mode == "N"}selected="selected"{/if}>{__("disable")}</option>
        </select>
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="order_prefix">{__("order_prefix")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" >
    </div>
</div>
