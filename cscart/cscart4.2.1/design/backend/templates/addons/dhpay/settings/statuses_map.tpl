{if fn_allowed_for('ULTIMATE') && !$runtime.company_id || $runtime.simple_ultimate || fn_allowed_for('MULTIVENDOR')}
<div id="text_dhpay_status_map" class="in collapse">
    {assign var="statuses" value=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses}
    <div class="control-group">
        <label class="control-label" for="elm_dhpay_refunded">{__("refunded")}:</label>
        <div class="controls">
            <select name="dhpay_settings[dhpay_statuses][refunded]" id="elm_dhpay_refunded">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($dhpay_settings.dhpay_statuses.refunded) && $dhpay_settings.dhpay_statuses.refunded == $k) || (!isset($dhpay_settings.dhpay_statuses.refunded) && $k == 'I')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_dhpay_completed">{__("completed")}:</label>
        <div class="controls">
            <select name="dhpay_settings[dhpay_statuses][completed]" id="elm_dhpay_completed">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($dhpay_settings.dhpay_statuses.completed) && $dhpay_settings.dhpay_statuses.completed == $k) || (!isset($dhpay_settings.dhpay_statuses.completed) && $k == 'P')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_dhpay_pending">{__("pending")}:</label>
        <div class="controls">
            <select name="dhpay_settings[dhpay_statuses][pending]" id="elm_dhpay_pending">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($dhpay_settings.dhpay_statuses.pending) && $dhpay_settings.dhpay_statuses.pending == $k) || (!isset($dhpay_settings.dhpay_statuses.pending) && $k == 'O')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_dhpay_canceled_reversal">{__("canceled_reversal")}:</label>
        <div class="controls">
            <select name="dhpay_settings[dhpay_statuses][canceled_reversal]" id="elm_dhpay_canceled_reversal">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($dhpay_settings.dhpay_statuses.canceled_reversal) && $dhpay_settings.dhpay_statuses.canceled_reversal == $k) || (!isset($dhpay_settings.dhpay_statuses.canceled_reversal) && $k == 'I')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_dhpay_created">{__("created")}:</label>
        <div class="controls">
            <select name="dhpay_settings[dhpay_statuses][created]" id="elm_dhpay_created">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($dhpay_settings.dhpay_statuses.created) && $dhpay_settings.dhpay_statuses.created == $k) || (!isset($dhpay_settings.dhpay_statuses.created) && $k == 'O')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_dhpay_denied">{__("denied")}:</label>
        <div class="controls">
            <select name="dhpay_settings[dhpay_statuses][denied]" id="elm_dhpay_denied">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($dhpay_settings.dhpay_statuses.denied) && $dhpay_settings.dhpay_statuses.denied == $k) || (!isset($dhpay_settings.dhpay_statuses.denied) && $k == 'I')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_dhpay_expired">{__("expired")}:</label>
        <div class="controls">
            <select name="dhpay_settings[dhpay_statuses][expired]" id="elm_dhpay_expired">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($dhpay_settings.dhpay_statuses.expired) && $dhpay_settings.dhpay_statuses.expired == $k) || (!isset($dhpay_settings.dhpay_statuses.expired) && $k == 'F')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_dhpay_reversed">{__("reversed")}:</label>
        <div class="controls">
            <select name="dhpay_settings[dhpay_statuses][reversed]" id="elm_dhpay_reversed">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($dhpay_settings.dhpay_statuses.reversed) && $dhpay_settings.dhpay_statuses.reversed == $k) || (!isset($dhpay_settings.dhpay_statuses.reversed) && $k == 'I')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_dhpay_processed">{__("processed")}:</label>
        <div class="controls">
            <select name="dhpay_settings[dhpay_statuses][processed]" id="elm_dhpay_processed">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($dhpay_settings.dhpay_statuses.processed) && $dhpay_settings.dhpay_statuses.processed == $k) || (!isset($dhpay_settings.dhpay_statuses.processed) && $k == 'P')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_dhpay_voided">{__("voided")}:</label>
        <div class="controls">
            <select name="dhpay_settings[dhpay_statuses][voided]" id="elm_dhpay_voided">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($dhpay_settings.dhpay_statuses.voided) && $dhpay_settings.dhpay_statuses.voided == $k) || (!isset($dhpay_settings.dhpay_statuses.voided) && $k == 'P')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="elm_dhpay_voided">{__("failed")}:</label>
        <div class="controls">
            <select name="dhpay_settings[dhpay_statuses][failed]" id="elm_dhpay_failed">
                {foreach from=$statuses item="s" key="k"}
                <option value="{$k}" {if (isset($dhpay_settings.dhpay_statuses.failed) && $dhpay_settings.dhpay_statuses.failed == $k) || (!isset($dhpay_settings.dhpay_statuses.failed) && $k == 'F')}selected="selected"{/if}>{$s}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>
{/if}