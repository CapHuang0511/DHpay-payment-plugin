{if fn_allowed_for('ULTIMATE') && !$runtime.company_id || $runtime.simple_ultimate || fn_allowed_for('MULTIVENDOR')}
    <div id="text_dhpay_config_data" class="in collapse">
        <div class="control-group">
            <label class="control-label" for="elm_dhpay_merchant_id">{__("dhpay_merchant_id")}:</label>
            <div class="controls">
                <input name="dhpay_settings[dhpay_config_data][merchant_id]" value="{if (isset($dhpay_settings.dhpay_config_data.merchant_id))}{$dhpay_settings.dhpay_config_data.merchant_id}{/if}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="elm_dhpay_private_key">{__("dhpay_private_key")}:</label>
            <div class="controls">
                <input name="dhpay_settings[dhpay_config_data][private_key]" value="{if (isset($dhpay_settings.dhpay_config_data.private_key))}{$dhpay_settings.dhpay_config_data.private_key}{/if}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="elm_dhpay_test_mode">{__("dhpay_test_mode")}:</label>
            <div class="controls">
                <select name="dhpay_settings[dhpay_config_data][test_mode]" id="elm_dhpay_test_mode">
                    <option value="Live" {if (isset($dhpay_settings.dhpay_config_data.test_mode) && $dhpay_settings.dhpay_config_data.test_mode == 'Live')}selected="selected"{/if}>Live</option>
                    <option value="Test" {if (isset($dhpay_settings.dhpay_config_data.test_mode) && $dhpay_settings.dhpay_config_data.test_mode == 'Test')}selected="selected"{/if}>Test</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="elm_dhpay_checkout_method">{__("dhpay_checkout_method")}:</label>
            <div class="controls">
                <select name="dhpay_settings[dhpay_config_data][checkout_method]" id="elm_dhpay_checkout_method">
                    <option value="Redirect" {if (isset($dhpay_settings.dhpay_config_data.checkout_method) && $dhpay_settings.dhpay_config_data.checkout_method == 'Redirect')}selected="selected"{/if}>Redirect</option>
                    <option value="Iframe" {if (isset($dhpay_settings.dhpay_config_data.checkout_method) && $dhpay_settings.dhpay_config_data.checkout_method == 'Iframe')}selected="selected"{/if}>Iframe</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_dhpay_style_layout">{__("dhpay_style_layout")}:</label>
            <div class="controls">
                <select name="dhpay_settings[dhpay_config_data][style_layout]" id="elm_dhpay_style_layout">
                    <option value="Vertical" {if (isset($dhpay_settings.dhpay_config_data.style_layout) && $dhpay_settings.dhpay_config_data.style_layout == 'Vertical')}selected="selected"{/if}>Vertical</option>
                    <option value="Horizontal" {if (isset($dhpay_settings.dhpay_config_data.style_layout) && $dhpay_settings.dhpay_config_data.style_layout == 'Horizontal')}selected="selected"{/if}>Horizontal</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="elm_dhpay_style_title">{__("dhpay_style_title")}:</label>
            <div class="controls">
                <input name="dhpay_settings[dhpay_config_data][style_title]" value="{if (isset($dhpay_settings.dhpay_config_data.style_title))}{$dhpay_settings.dhpay_config_data.style_title}{/if}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="elm_dhpay_style_body">{__("dhpay_style_body")}:</label>
            <div class="controls">
                <input name="dhpay_settings[dhpay_config_data][style_body]" value="{if (isset($dhpay_settings.dhpay_config_data.style_body))}{$dhpay_settings.dhpay_config_data.style_body}{/if}" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="elm_dhpay_style_button">{__("dhpay_style_button")}:</label>
            <div class="controls">
                <input name="dhpay_settings[dhpay_config_data][style_button]" value="{if (isset($dhpay_settings.dhpay_config_data.style_button))}{$dhpay_settings.dhpay_config_data.style_button}{/if}" />
            </div>
        </div>
    </div>
{/if}
